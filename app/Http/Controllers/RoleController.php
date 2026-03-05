<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{


    public function index()
    {
        //  Roles descending by created_at
        $roles = Role::orderBy('created_at', 'desc')->paginate(10);

        //  Users descending by created_at
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        return view('roles.index', compact('roles', 'users'));
    }


    public function create()
    {
        $mode = 'add';
        return view('roles.add-roles', compact('mode'));
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'name' => 'required|string|unique:roles,name',
    //         'description' => 'nullable|string',
    //     ]);

    //     Role::create($validated);

    //     return redirect()
    //         ->route('roles.index')
    //         ->with('success', 'Role created successfully.');
    // }


    // public function store(Request $request)
    // {
    //     Log::info('Role Store Request', [
    //         'request' => $request->all()
    //     ]);

    //     $validated = $request->validate([
    //         'name' => 'required|string|unique:roles,name',
    //         'description' => 'nullable|string',
    //     ]);

    //     $roleName = strtolower($validated['name']); // lowercase for uniform comparison
    //     $existingRoles = Role::pluck('name')->map(fn($r) => strtolower($r))->toArray();

    //     // 🔹 Sequence Enforcement
    //     if (!in_array('master', $existingRoles)) {
    //         // Master role must be first
    //         if ($roleName !== 'master') {
    //             return back()
    //                 ->withInput()
    //                 ->with('error', 'Please add Master role first.');
    //         }
    //     } elseif (!in_array('district', $existingRoles)) {
    //         // Master exists, District must come next
    //         if ($roleName !== 'district') {
    //             return back()
    //                 ->withInput()
    //                 ->with('error', 'Please add District role next.');
    //         }
    //     } elseif (!in_array('taluka', $existingRoles)) {
    //         // District exists, Taluka must come next
    //         if ($roleName !== 'taluka') {
    //             return back()
    //                 ->withInput()
    //                 ->with('error', 'Please add Taluka role next.');
    //         }
    //     }
    //     // 🔹 After Master → District → Taluka, any other role is allowed freely

    //     Role::create($validated);

    //     return redirect()->route('roles.index')
    //         ->with('success', 'Role created successfully.');
    // }

    public function store(Request $request)
    {
        Log::info('Role Store Request Received', [
            'request_data' => $request->all()
        ]);

        try {

            // Validation
            $validated = $request->validate([
                'name' => 'required|string|unique:roles,name',
                'description' => 'nullable|string',
            ]);

            Log::info('Role Validation Passed', [
                'validated_data' => $validated
            ]);

            $roleName = strtolower($validated['name']);

            $existingRoles = Role::pluck('name')
                ->map(fn($r) => strtolower($r))
                ->toArray();

            Log::info('Existing Roles Fetched', [
                'existing_roles' => $existingRoles
            ]);

            // 🔹 Sequence Enforcement
            // if (!in_array('master', $existingRoles)) {

            //     Log::warning('Master role not found in database');

            //     if ($roleName !== 'master') {

            //         Log::error('Invalid Role Order Attempt', [
            //             'attempted_role' => $roleName,
            //             'message' => 'Master role must be first'
            //         ]);

            //         return back()
            //             ->withInput()
            //             ->with('error', 'Please add Master role first.');
            //     }
            // }

            // $roleName = strtolower($validated['name']);

            // $existingRoles = Role::pluck('name')
            //     ->map(fn($r) => strtolower($r))
            //     ->toArray();

            // 🔹 If no roles exist yet
            $filteredRoles = array_diff($existingRoles, ['super admin']);

            if (empty($filteredRoles)) {

                if ($roleName !== 'master') {

                    Log::error('First role must be Master', [
                        'attempted_role' => $roleName
                    ]);

                    return back()
                        ->withInput()
                        ->with('error', 'Please add Master role first.');
                }
            } elseif (!in_array('district', $existingRoles)) {

                Log::warning('District role not found, expecting District');

                if ($roleName !== 'district') {

                    Log::error('Invalid Role Order Attempt', [
                        'attempted_role' => $roleName,
                        'message' => 'District role must be second'
                    ]);

                    return back()
                        ->withInput()
                        ->with('error', 'Please add District role next.');
                }
            } elseif (!in_array('taluka', $existingRoles)) {

                Log::warning('Taluka role not found, expecting Taluka');

                if ($roleName !== 'taluka') {

                    Log::error('Invalid Role Order Attempt', [
                        'attempted_role' => $roleName,
                        'message' => 'Taluka role must be third'
                    ]);

                    return back()
                        ->withInput()
                        ->with('error', 'Please add Taluka role next.');
                }
            }

            // Role Create
            $role = Role::create($validated);

            Log::info('Role Created Successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {

            Log::error('Role Store Failed', [
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while creating role.');
        }
    }

    public function show($id)
    {
        $mode = 'show';
        $role = Role::findOrFail($id);
        return view('roles.add-roles', compact('role', 'mode'));
    }

    public function edit($id)
    {
        $mode = 'edit';
        $role = Role::findOrFail($id);
        return view('roles.add-roles', compact('role', 'mode'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id); // <-- Fetch model
        $role->update($request->only(['name', 'description']));

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id)
    {
        Log::info('Role Delete Request Received', ['role_id' => $id]);

        $role = Role::find($id); // normal find for web requests

        if (!$role) {
            Log::warning('Role Not Found for Delete', ['role_id' => $id]);
            return redirect()->route('roles.index')
                ->with('error', 'Role not found');
        }

        try {
            // Soft Delete
            $role->delete();

            Log::info('Role Soft Deleted Successfully', ['role_id' => $id]);

            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Role Delete Error', [
                'role_id' => $id,
                'error'   => $e->getMessage()
            ]);

            return redirect()->route('roles.index')
                ->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
