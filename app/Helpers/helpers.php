<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('hasPermission')) {
    function hasPermission($module, $action = null)
    {
        $user = Auth::user();
        if (!$user) return false;

        $rolePermission = \App\Models\RolePermission::where('role_id', $user->role_id)
            ->where('admin_id', $user->admin_id ?? $user->id)
            ->first();

        if (!$rolePermission) return false;

        $permissions = json_decode($rolePermission->permissions, true);

        if (!isset($permissions[$module])) return false;

        if ($action === null) return true;

        return in_array($action, $permissions[$module]);
    }
}
