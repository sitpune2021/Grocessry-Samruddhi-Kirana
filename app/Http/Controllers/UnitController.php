<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::latest()->paginate(10);
        return view('menus.unit.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.unit.add-unit', ['mode' => 'add']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validated = $request->validate([
                'name'       => 'required|string|max:255|unique:units,name',
                'short_name' => 'required|string|max:50|unique:units,short_name',
            ]);

            DB::beginTransaction();

            $unit = Unit::create([
                'name'       => $validated['name'],
                'short_name' => strtoupper($validated['short_name']),
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            // Success Log
            Log::info('Unit created successfully', [
                'unit_id' => $unit->id,
                'user_id' => Auth::id(),
                'ip'      => $request->ip(),
            ]);

            return redirect()
                ->route('units.index')
                ->with('success', 'Unit created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            // Validation Error Log
            Log::warning('Unit validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);

            throw $e; // Laravel will handle redirect with errors

        } catch (\Exception $e) {

            DB::rollBack();

            // Exception Log
            Log::error('Error while creating unit', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while saving the unit.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $units = Unit::findOrFail($id);
        $mode = 'show';
        return view('menus.unit.add-unit', compact('units', 'mode'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $units = Unit::findOrFail($id);
        $mode = 'edit';
        return view('menus.unit.add-unit', compact('units', 'mode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Fetch unit
            $unit = Unit::findOrFail($id);

            // Validate request
            $validated = $request->validate([
                'name'       => 'required|string|max:255|unique:units,name,' . $unit->id,
                'short_name' => 'required|string|max:50|unique:units,short_name,' . $unit->id,
            ]);

            DB::beginTransaction();

            // Update unit
            $unit->update([
                'name'       => $validated['name'],
                'short_name' => strtoupper($validated['short_name']),
                'updated_by' => Auth::id(), // optional if column exists
            ]);

            DB::commit();

            // Success log
            Log::info('Unit updated successfully', [
                'unit_id' => $unit->id,
                'user_id' => Auth::id(),
                'ip'      => $request->ip(),
            ]);

            return redirect()
                ->route('units.index')
                ->with('success', 'Unit updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            Log::warning('Unit not found while updating', [
                'unit_id' => $id,
                'user_id' => Auth::id(),
            ]);

            return redirect()
                ->route('units.index')
                ->with('error', 'Unit not found.');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Unit update validation failed', [
                'unit_id' => $id,
                'errors'  => $e->errors(),
            ]);

            throw $e; // Laravel handles redirect back with errors

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error while updating unit', [
                'unit_id' => $id,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the unit.');
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
