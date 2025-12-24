<?php

use Illuminate\Support\Facades\Auth;
use App\Models\RolePermission;
if (!function_exists('hasPermission')) {

    function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        // Super Admin (optional)
        if ($user->role_id == 1) {
            return true;
        }

        $rolePermission = \App\Models\RolePermission::where('role_id', $user->role_id)
            ->where('admin_id', $user->admin_id ?? $user->id)
            ->first();

        if (!$rolePermission || empty($rolePermission->permissions)) {
            return false;
        }

        $permissions = json_decode($rolePermission->permissions, true);

        return in_array($permission, $permissions);
    }
}
