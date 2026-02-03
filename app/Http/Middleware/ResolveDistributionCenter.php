<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Warehouse;
use App\Models\UserAddress;

class ResolveDistributionCenter
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * If already resolved, do nothing
         */
        if (session()->has('dc_warehouse_id')) {
            return $next($request);
        }

        /**
         * Logged-in user → default address (Flipkart style)
         */
        if (Auth::check()) {

            $address = UserAddress::where('user_id', Auth::id())
                ->where('is_default', 1)
                ->first();

            if ($address && $address->postcode) {

                $dc = $this->dcByPincode($address->postcode);

                if ($dc) {
                    session(['dc_warehouse_id' => $dc->id]);

                    Log::info('DC resolved from user default address', [
                        'user_id' => Auth::id(),
                        'postcode' => $address->postcode,
                        'dc_id' => $dc->id,
                        'dc_name' => $dc->name,
                    ]);

                    return $next($request);
                }

                Log::warning('No DC found for user default address pincode', [
                    'user_id' => Auth::id(),
                    'postcode' => $address->postcode,
                ]);
            }
        }

        /**
         * Manual pincode from session (Amazon style)
         */
        if (session()->has('user_pincode')) {

            $pincode = session('user_pincode');
            $dc = $this->dcByPincode($pincode);

            if ($dc) {
                session(['dc_warehouse_id' => $dc->id]);

                Log::info('DC resolved from manual pincode', [
                    'pincode' => $pincode,
                    'dc_id' => $dc->id,
                    'dc_name' => $dc->name,
                ]);
            } else {
                Log::warning('No DC found for manual pincode', [
                    'pincode' => $pincode,
                ]);
            }
        }

        return $next($request);
    }

    /**
     * Find distribution center by pincode
     */
    private function dcByPincode(string $pincode): ?Warehouse
    {
        return Warehouse::where('type', 'distribution_center')
            ->where('status', 'active')
            ->whereHas('servicePincodes', function ($q) use ($pincode) {
                $q->where('pincode', $pincode);
            })
            ->first();
    }
}
