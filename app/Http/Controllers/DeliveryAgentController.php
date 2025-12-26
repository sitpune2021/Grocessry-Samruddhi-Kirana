<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryAgent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeliveryAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch delivery agent vehicles with driver info, paginate 10 per page
        $agents = DeliveryAgent::latest()->paginate(10);

        return view('menus.delivery-agent.delivery-agent.index', compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        $agent = null;

        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('mode', 'agent'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'mobile'         => 'required|digits_between:10,15|unique:delivery_agents,mobile',
                'email'          => 'nullable|email|unique:delivery_agents,email',
                'vehicle_type'   => 'nullable|string|max:50',
                'vehicle_number' => 'nullable|string|max:50',
            ]);

            DB::beginTransaction();

            $agent = DeliveryAgent::create([
                ...$validated,
                'created_by' => Auth::id(),
                'status'     => 1,
            ]);

            DB::commit();

            Log::info('Delivery Agent created', [
                'agent_id' => $agent->id,
                'user_id'  => Auth::id(),
            ]);

            return redirect()->route('delivery-agents.index')
                ->with('success', 'Delivery Agent added successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delivery Agent create failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $agent = DeliveryAgent::findOrFail($id);
        $mode = 'view';

        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = DeliveryAgent::findOrFail($id);
        $mode = 'edit';

        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $agent = DeliveryAgent::findOrFail($id);

            $validated = $request->validate([
                'name'           => 'required|string|max:255',
                'mobile'         => 'required|digits_between:10,15|unique:delivery_agents,mobile,' . $agent->id,
                'email'          => 'nullable|email|unique:delivery_agents,email,' . $agent->id,
                'vehicle_type'   => 'nullable|string|max:50',
                'vehicle_number' => 'nullable|string|max:50',
                'status'         => 'required|boolean',
            ]);

            DB::beginTransaction();

            $agent->update($validated);

            DB::commit();

            Log::info('Delivery Agent updated', [
                'agent_id' => $agent->id,
                'user_id'  => Auth::id(),
            ]);

            return redirect()->route('delivery-agents.index')
                ->with('success', 'Delivery Agent updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Delivery Agent update failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Something went wrong');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $agent = DeliveryAgent::findOrFail($id);
            $agent->delete();

            Log::info('Delivery Agent deleted', [
                'agent_id' => $id,
                'user_id'  => Auth::id(),
            ]);

            return back()->with('success', 'Delivery Agent deleted');
        } catch (\Exception $e) {
            Log::error('Delivery Agent delete failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to delete');
        }
    }
}
