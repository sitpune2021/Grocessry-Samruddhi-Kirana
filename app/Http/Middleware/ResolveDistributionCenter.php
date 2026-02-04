<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\UserAddress;

class ResolveDistributionCenter
{
    public function handle($request, Closure $next)
    {
        // If already set, continue
        if (session()->has('dc_warehouse_id')) {
            return $next($request);
        }

        $dc = null;

        // 1️⃣ Logged-in user: use default address
        if (Auth::check()) {
            $address = UserAddress::where('user_id', Auth::id())
                ->where('is_default', 1)
                ->whereNotNull('postcode')
                ->first();

            if ($address) {
                $dc = $this->findDC($address->postcode);
                if ($dc) {
                    session([
                        'dc_warehouse_id' => $dc->id,
                        'user_pincode' => $address->postcode
                    ]);
                    return $next($request);
                }
            }
        }

        // 2️⃣ Guest user: use session pincode
        if (session()->has('user_pincode')) {
            $dc = $this->findDC(session('user_pincode'));
            if ($dc) {
                session(['dc_warehouse_id' => $dc->id]);
            }
        }

        return $next($request);
    }

    private function findDC($pincode)
    {
        return Warehouse::where('type', 'distribution_center')
            ->where('status', 'active')
            ->whereHas('servicePincodes', fn($q) => $q->where('pincode', $pincode))
            ->first();
    }
}
