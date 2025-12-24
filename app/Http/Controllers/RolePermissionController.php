<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

class RolePermissionController extends Controller
{

    public function RolePermission()
    {
        $roles = Role::all();
        return view('userProfile.rolepermission', compact('roles'));
    }

    public function store(Request $request)
    {
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

        if (empty($newPermissions)) {
            return back()->withErrors([
                'permissions' => 'Please select at least one permission'
            ]);
        }

        // Fetch existing role permissions
        $rolePermission = RolePermission::where('role_id', $request->role_id)
            ->where('admin_id', Auth::id())
            ->first();

        if ($rolePermission) {
            // Merge old + new
            $mergedPermissions = collect($rolePermission->permissions)
                ->merge($newPermissions)
                ->unique()
                ->values()
                ->toArray();

            $rolePermission->update([
                'permissions' => $mergedPermissions
            ]);
        } else {
            // First time insert
            RolePermission::create([
                'role_id'     => $request->role_id,
                'admin_id'    => Auth::id(),
                'permissions' => $newPermissions
            ]);
        }

        return redirect()->back()->with('success', 'Permissions saved successfully!');
    }


    public function getRolePermissions($role_id)
    {
        $rolePermission = RolePermission::where('role_id', $role_id)
            ->where('admin_id', Auth::id())
            ->first();

        if ($rolePermission) {
            return response()->json([
                'status' => true,
                'permissions' => $rolePermission->permissions ?? []
            ]);
        }

        return response()->json([
            'status' => false,
            'permissions' => []
        ]);
    }
}
