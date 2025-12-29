<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryAgent;
use App\Models\GroceryShop;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DeliveryAgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch delivery agent vehicles with driver info, paginate 10 per page
        $agents = DeliveryAgent::with(['user', 'shop'])
            ->latest()
            ->paginate(10);

        return view('menus.delivery-agent.delivery-agent.index', compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';
        $agent = null;
        $shops = GroceryShop::where('status', 'active')->get();
        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('mode', 'agent', 'shops'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Delivery Agent store process started', [
            'requested_by' => Auth::id(),
            'ip' => $request->ip()
        ]);

        DB::beginTransaction();

        try {

            /* ----------------------------
         | 1. Validate Request
         ---------------------------- */
            Log::info('Validating delivery agent request');

            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'last_name'            => 'required|string|max:255',
                'mobile'          => 'required|digits:10|unique:users,mobile',
                'email'           => 'nullable|email|unique:users,email',
                'password'        => 'nullable|min:6',

                'shop_id'         => 'required|exists:grocery_shops,id',
                'dob'             => 'nullable|date',
                'gender'          => 'nullable|in:male,female',
                'address'         => 'nullable|string',
                'active_status'   => 'required|boolean',

                'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'aadhaar_card'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'driving_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            Log::info('Validation passed', [
                'mobile' => $validated['mobile'],
                'email'  => $validated['email'] ?? null
            ]);

            /* ----------------------------
         | 2. Get Delivery Agent Role
         ---------------------------- */
            Log::info('Fetching Delivery Agent role');

            $role = Role::where('name', 'Delivery Agent')->firstOrFail();

            Log::info('Role found', [
                'role_id' => $role->id
            ]);

            /* ----------------------------
         | 3. Create User
         ---------------------------- */
            Log::info('Creating user record');
            $profileImage = null;

            if ($request->hasFile('profile_photo')) {
                $profileImage = $request->file('profile_image')
                    ->store('profile_photos', 'public');

                Log::info('Profile image uploaded', [
                    'path' => $profileImage
                ]);
            }

            $user = User::create([
                'first_name'      => $validated['name'],
                'last_name'      => $validated['last_name'],
                'email'     => $validated['email'] ?? null,
                'mobile'    => $validated['mobile'],
                'password'  => Hash::make('Agent@123'),
                'role_id'   => $role->id,
                'profile_photo'   => $profileImage,

            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id
            ]);

            /* ----------------------------
         | 4. Upload Files
         ---------------------------- */
            Log::info('Uploading delivery agent files');


            $aadhaarPath  = null;
            $licensePath  = null;

            if ($request->hasFile('aadhaar_card')) {
                $aadhaarPath = $request->file('aadhaar_card')
                    ->store('delivery_agents/aadhaar', 'public');

                Log::info('Aadhaar uploaded', [
                    'path' => $aadhaarPath
                ]);
            }

            if ($request->hasFile('driving_license')) {
                $licensePath = $request->file('driving_license')
                    ->store('delivery_agents/license', 'public');

                Log::info('Driving license uploaded', [
                    'path' => $licensePath
                ]);
            }

            /* ----------------------------
         | 5. Create Delivery Agent
         ---------------------------- */
            Log::info('Creating delivery agent record');

            DeliveryAgent::create([
                'user_id'         => $user->id,
                'shop_id'         => $validated['shop_id'],
                'dob'             => $validated['dob'] ?? null,
                'gender'          => $validated['gender'] ?? null,
                'address'         => $validated['address'] ?? null,

                'aadhaar_card'    => $aadhaarPath,
                'driving_license' => $licensePath,
                'active_status'   => $validated['active_status'],
                'created_by'      => Auth::id(),
            ]);

            Log::info('Delivery agent created successfully', [
                'user_id' => $user->id,
                'shop_id' => $validated['shop_id']
            ]);

            DB::commit();

            Log::info('Transaction committed successfully');

            return redirect()
                ->route('delivery-agents.index')
                ->with('success', 'Delivery Agent created successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error while creating delivery agent', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->except(['password'])
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $agent = DeliveryAgent::with('user')->findOrFail($id);
        $shops = GroceryShop::where('status',  'active')->get();
        $mode = 'view';
        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode', 'shops'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = DeliveryAgent::with('user')->findOrFail($id);
        $shops = GroceryShop::where('status',  'active')->get();
        $mode = 'edit';

        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode', 'shops'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            Log::info('Delivery Agent Update Started', [
                'delivery_agent_id' => $id,
                'request_data' => $request->all(),
            ]);

            $agent = DeliveryAgent::with('user')->findOrFail($id);

            Log::info('Delivery Agent Found', [
                'agent_id' => $agent->id,
                'user_id'  => optional($agent->user)->id,
            ]);

            /* ---------------- Validation ---------------- */
            $validated = $request->validate([
                'shop_id'       => 'required|exists:grocery_shops,id',
                'name'          => 'required|string|max:255',
                'last_name'     => 'required|string|max:255',
                'dob'           => 'nullable|date',
                'gender'        => 'nullable|in:male,female',
                'address'       => 'nullable|string',
                'active_status' => 'required|boolean',

                'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'aadhaar_card'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'driving_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            Log::info('Validation Passed', $validated);

            /* ---------------- Update User ---------------- */
            $user = $agent->user;

            if (!$user) {
                Log::error('User not linked with delivery agent', [
                    'agent_id' => $agent->id,
                ]);
                throw new \Exception('User not linked with delivery agent');
            }

            $user->update([
                'first_name' => $validated['name'],
                'last_name'  => $validated['last_name'],
                'mobile'     => $request->mobile, // if coming from form
                'email'      => $request->email ?? null,
            ]);

            Log::info('User Updated Successfully', [
                'user_id' => $user->id,
            ]);

            /* ---------------- File Uploads ---------------- */
            if ($request->hasFile('profile_image')) {
                $agent->profile_image = $request->file('profile_image')
                    ->store('profile_photo', 'public');

                Log::info('Profile image uploaded');
            }

            if ($request->hasFile('aadhaar_card')) {
                $agent->aadhaar_card = $request->file('aadhaar_card')
                    ->store('delivery_agents/aadhaar', 'public');

                Log::info('Aadhaar uploaded');
            }

            if ($request->hasFile('driving_license')) {
                $agent->driving_license = $request->file('driving_license')
                    ->store('delivery_agents/license', 'public');

                Log::info('Driving license uploaded');
            }

            /* ---------------- Update Agent ---------------- */
            $agent->update([
                'shop_id'       => $validated['shop_id'],
                'dob'           => $validated['dob'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'address'       => $validated['address'] ?? null,
                'active_status' => $validated['active_status'],
                'updated_by'    => Auth::id(),
            ]);

            Log::info('Delivery Agent Updated Successfully', [
                'agent_id' => $agent->id,
            ]);

            DB::commit();

            Log::info('Delivery Agent Update Completed');

            return redirect()
                ->route('delivery-agents.index')
                ->with('success', 'Delivery Agent updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Delivery Agent Update Failed', [
                'agent_id' => $id,
                'error'    => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
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
