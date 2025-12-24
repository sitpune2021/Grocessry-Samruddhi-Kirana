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

        // ✅ Clean permissions (remove null, empty, duplicates)
        $permissions = collect($request->permissions)
            ->filter()          // remove null / empty
            ->unique()          // remove duplicates
            ->values()
            ->toArray();

        if (empty($permissions)) {
            return back()->withErrors([
                'permissions' => 'Please select at least one permission'
            ]);
        }

        // ✅ Save / Update permissions
        RolePermission::updateOrCreate(
            [
                'role_id'  => $request->role_id,
                'admin_id' => Auth::id(),
            ],
            [
                'permissions' => $permissions, // JSON column (NO json_encode)
            ]
        );

        return redirect()->back()->with('success', 'Permissions saved successfully!');
    }

    public function getRolePermissions($role_id)
    {
       
        $rolePermission = RolePermission::where('role_id', $role_id)
            ->where('admin_id', Auth::id())
            ->first();

        if ($rolePermission) {
            $permissions = json_decode($rolePermission->permissions, true);
            return response()->json([
                'status' => true,
                'permissions' => $permissions ?? []
            ]);
        }

        return response()->json(['status' => false, 'permissions' => []]);
    }
}
