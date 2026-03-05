<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;

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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles')->where(fn($query) => $query->whereNull('deleted_at')),
            ],
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Please enter role name',
            'name.unique' => 'This role already exists',
        ]);


        $roleName = strtolower($validated['name']); // lowercase for uniform comparison
        $existingRoles = Role::pluck('name')->map(fn($r) => strtolower($r))->toArray();

        // 🔹 Sequence Enforcement
        if (!in_array('master admin', $existingRoles)) {
            // Master role must be first
            if ($roleName !== 'master admin') {
                return back()
                    ->withInput()
                    ->with('error', 'Please add Master admin role first.');
            }
        } elseif (!in_array('district admin', $existingRoles)) {
            // Master exists, District must come next
            if ($roleName !== 'district admin') {
                return back()
                    ->withInput()
                    ->with('error', 'Please add District admin role next.');
            }
        } elseif (!in_array('taluka admin', $existingRoles)) {
            // District exists, Taluka must come next
            if ($roleName !== 'taluka admin') {
                return back()
                    ->withInput()
                    ->with('error', 'Please add Taluka admin role next.');
            }
        }
        // 🔹 After Master → District → Taluka, any other role is allowed freely

        Role::create($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
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
