<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseServicePincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WarehouseServicePincodeController extends Controller
{
    /**
     * Resolve active Distribution Center
     */
    private function resolveWarehouse(Request $request)
    {
        $user = Auth::user();

        // SUPER ADMIN
        if ($user->role_id == 1) {

            if ($request->filled('warehouse_id')) {
                session(['dc_warehouse_id' => $request->warehouse_id]);

                Log::info('Super Admin selected distribution center', [
                    'user_id' => $user->id,
                    'warehouse_id' => $request->warehouse_id,
                ]);
            }

            if (session()->has('dc_warehouse_id')) {
                $warehouse = Warehouse::where('type', 'distribution_center')
                    ->find(session('dc_warehouse_id'));

                if (!$warehouse) {
                    Log::warning('Invalid DC in session for Super Admin', [
                        'user_id' => $user->id,
                        'dc_warehouse_id' => session('dc_warehouse_id'),
                    ]);
                }

                return $warehouse;
            }

            return null; // UI will show dropdown
        }

        // 👤 NORMAL ADMIN
        $warehouse = Warehouse::where('type', 'distribution_center')
            ->find($user->warehouse_id);

        if (!$warehouse) {
            Log::error('Normal admin has invalid or non-DC warehouse', [
                'user_id' => $user->id,
                'warehouse_id' => $user->warehouse_id,
            ]);
        }

        return $warehouse;
    }

    /**
     * Show service areas
     */
    public function index(Request $request)
    {
        $warehouse = $this->resolveWarehouse($request);

        $distributionCenters = collect();
        $pincodes = collect();

        if (Auth::user()->role_id == 1) {
            $distributionCenters = Warehouse::where('type', 'distribution_center')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        }

        if ($warehouse) {
            $pincodes = $warehouse->servicePincodes()
                ->orderBy('pincode')
                ->get();
        }

        return view('warehouse.service_areas', compact(
            'warehouse',
            'distributionCenters',
            'pincodes'
        ));
    }

    /**
     * Add pincode
     */
    public function store(Request $request)
    {
        $warehouse = $this->resolveWarehouse($request);

        if (!$warehouse) {
            Log::notice('Attempt to add pincode without selecting DC', [
                'user_id' => Auth::id(),
            ]);

            return back()->withErrors([
                'warehouse_id' => 'Please select a Distribution Center first'
            ]);
        }

        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        $created = WarehouseServicePincode::firstOrCreate([
            'warehouse_id' => $warehouse->id,
            'pincode'      => $request->pincode
        ]);

        Log::info('Service pincode added', [
            'user_id' => Auth::id(),
            'warehouse_id' => $warehouse->id,
            'pincode' => $request->pincode,
            'created' => $created->wasRecentlyCreated,
        ]);

        return back()->with('success', 'Pincode added successfully');
    }

    /**
     * Remove pincode
     */
    public function destroy(WarehouseServicePincode $pincode)
    {
        $warehouse = $this->resolveWarehouse(request());

        if (!$warehouse) {
            Log::warning('Attempt to delete pincode without DC context', [
                'user_id' => Auth::id(),
                'pincode_id' => $pincode->id,
            ]);

            return back()->withErrors([
                'error' => 'Please select a Distribution Center first'
            ]);
        }

        if ($pincode->warehouse_id !== $warehouse->id) {
            Log::warning('Unauthorized pincode delete attempt', [
                'user_id' => Auth::id(),
                'pincode_id' => $pincode->id,
                'pincode_warehouse_id' => $pincode->warehouse_id,
                'active_warehouse_id' => $warehouse->id,
            ]);

            return back()->withErrors([
                'error' => 'You are not allowed to remove this pincode'
            ]);
        }

        $pincode->delete();

        Log::info('Service pincode removed', [
            'user_id' => Auth::id(),
            'warehouse_id' => $warehouse->id,
            'pincode' => $pincode->pincode,
        ]);

        return back()->with('success', 'Pincode removed successfully');
    }
}
