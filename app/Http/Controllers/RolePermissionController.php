<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{

    public function RolePermission()
    {
        Log::info('RolePermission page opened', [
            'admin_id' => Auth::id()
        ]);

        $roles = Role::all();

        Log::info('Roles fetched', [
            'roles_count' => $roles->count()
        ]);

        return view('userProfile.rolepermission', compact('roles'));
    }

    public function store(Request $request)
    {
        Log::info('Store permission request received', [
            'admin_id' => Auth::id(),
            'role_id' => $request->role_id,
            'permissions' => $request->permissions
        ]);

        $request->validate([
            'role_id'     => 'required|exists:roles,id',
            'permissions' => 'required|array|min:1',
        ]);

        // New permissions from form
        $newPermissions = collect($request->permissions)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        Log::info('Filtered new permissions', [
            'permissions' => $newPermissions
        ]);

        if (empty($newPermissions)) {

            Log::warning('No permissions selected');

            return back()->withErrors([
                'permissions' => 'Please select at least one permission'
            ]);
        }

        // Fetch existing role permissions
        $rolePermission = RolePermission::where('role_id', $request->role_id)
            ->where('admin_id', Auth::id())
            ->first();

        Log::info('Existing role permission check', [
            'exists' => $rolePermission ? true : false
        ]);

        if ($rolePermission) {

            Log::info('Updating existing permissions', [
                'old_permissions' => $rolePermission->permissions
            ]);

            // Merge old + new
            $mergedPermissions = collect($rolePermission->permissions)
                ->merge($newPermissions)
                ->unique()
                ->values()
                ->toArray();

            Log::info('Merged permissions', [
                'merged_permissions' => $mergedPermissions
            ]);

            $rolePermission->update([
                'permissions' => $mergedPermissions
            ]);

            Log::info('Permissions updated successfully');
        } else {

            Log::info('Creating new role permissions');

            // First time insert
            RolePermission::create([
                'role_id'     => $request->role_id,
                'admin_id'    => Auth::id(),
                'permissions' => $newPermissions
            ]);

            Log::info('New role permissions created', [
                'role_id' => $request->role_id
            ]);
        }
        return redirect()->back()->with('success', 'Permissions saved successfully!');
    }


    public function getRolePermissions($role_id)
    {
        Log::info('Fetching permissions for role', [
            'role_id' => $role_id,
            'admin_id' => Auth::id()
        ]);

        $rolePermission = RolePermission::where('role_id', $role_id)
            ->where('admin_id', Auth::id())
            ->first();

        if ($rolePermission) {

            Log::info('Permissions found', [
                'permissions' => $rolePermission->permissions
            ]);

            return response()->json([
                'status' => true,
                'permissions' => $rolePermission->permissions ?? []
            ]);
        }

        Log::warning('No permissions found for role', [
            'role_id' => $role_id
        ]);

        return response()->json([
            'status' => false,
            'permissions' => []
        ]);
    }
}