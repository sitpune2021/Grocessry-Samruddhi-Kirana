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
        $roles = Role::latest()->paginate(10);
        $users = User::latest()->paginate(10);

        return view('roles.index', compact('roles', 'users'));
    }


    public function create()
    {
        $mode = 'add';
        return view('roles.add-roles', compact('mode'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        Role::create($validated);

        return redirect()
            ->route('roles.index')
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
