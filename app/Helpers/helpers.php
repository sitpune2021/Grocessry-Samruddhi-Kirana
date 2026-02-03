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

        $rolePermission = RolePermission::where('role_id', $user->role_id)
            ->first();

        if (!$rolePermission || empty($rolePermission->permissions)) {
            return false;
        }

        return in_array($permission, $rolePermission->permissions, true);
    }

    function getDiscountPercentage($mrp, $finalPrice)
{
    if ($mrp <= 0 || $finalPrice >= $mrp) {
        return 0;
    }

    return round((($mrp - $finalPrice) / $mrp) * 100);
}

}
