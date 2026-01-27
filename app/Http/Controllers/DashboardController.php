<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;
use App\Models\Batch;
use App\Models\CustomerOrderReturn;
use App\Models\ProductBatch;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\GroceryShop;
use App\Models\User;
use App\Models\WarehouseStockReturn;

class DashboardController extends Controller
{


    public function index()
    {
        $user = auth()->user()->load('warehouse');
        $warehouse = $user->warehouse;

        // =======================
        // Counts
        // =======================
        $categoryCount = Category::count();
        $ProductCount = Product::count();
        $BatchCount = Batch::count();
        $WarehouseCount = Warehouse::count();
        $StockMovementCount = WarehouseStock::count();
        $WarehouseTransferCount = WarehouseTransfer::count();

        // Only show Users count for admin roles (optional)
        $UserCount = in_array($user->role_id, [1, 2])
            ? User::count()
            : 1; // login user only

        // =======================
        // Expiry alerts
        // =======================
        $today = now();

        $expiredCount = ProductBatch::where('quantity', '>', 0)
            ->whereDate('expiry_date', '<', $today)
            ->count();

        $expiringSoonCount = ProductBatch::where('quantity', '>', 0)
            ->whereBetween('expiry_date', [$today, $today->copy()->addDays(7)])
            ->count();


        // =======================
        // Warehouse / Shop Lists (login user)
        // =======================
        $warehouseDistrict = Warehouse::where('status', 'active')
            ->orderBy('type')
            ->pluck('name');
        $warehouseTaluka = collect();
        $shops = collect();

        if ($warehouse) {
            if ($warehouse->type === 'district') {
                $warehouseDistrict = Warehouse::where('district_id', $warehouse->district_id)
                    ->pluck('name');
                $warehouseTaluka = Warehouse::where('district_id', $warehouse->district_id)
                    ->whereNotNull('taluka_id')
                    ->pluck('name');
                // FIXED LINE: shops related to taluka_id
                $shops = GroceryShop::whereIn('taluka_id', Warehouse::where('district_id', $warehouse->district_id)->pluck('id'))
                    ->pluck('shop_name');
            }

            if ($warehouse->type === 'taluka') {
                $warehouseTaluka = Warehouse::where('id', $warehouse->id)->pluck('name');
                // FIXED LINE: shops related to this taluka
                $shops = GroceryShop::where('taluka_id', $warehouse->id)
                    ->pluck('shop_name');
            }
        }

        // =======================
        // Warehouse Stock Returns (login user)
        // =======================
        $warehouseStockReturnCount = 0;

        if ($warehouse) {
            if ($warehouse->type === 'taluka') {
                $warehouseStockReturnCount = WarehouseStockReturn::where('from_warehouse_id', $warehouse->id)
                    ->where('status', '!=', 'received')
                    ->count();
            }

            if ($warehouse->type === 'district') {
                $warehouseStockReturnCount = WarehouseStockReturn::where(function ($q) use ($warehouse) {
                    $q->where('from_warehouse_id', $warehouse->id)
                        ->orWhere('to_warehouse_id', $warehouse->id);
                })->where('status', '!=', 'received')->count();
            }
        }

        $threshold = 100;

        $totalLowStock = WarehouseStock::where('quantity', '<=', $threshold)->count();

        $warehouseWise = WarehouseStock::selectRaw(
            'warehouse_id, COUNT(*) as total'
        )
            ->where('quantity', '<=', $threshold)
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get();

        // =======================
        // Send to view
        // =======================
        return view('dashboard.dashboard', compact(
            'categoryCount',
            'ProductCount',
            'BatchCount',
            'WarehouseCount',
            'StockMovementCount',
            'WarehouseTransferCount',
            'UserCount',
            'expiredCount',
            'expiringSoonCount',
            'warehouseDistrict',
            'warehouseTaluka',
            'shops',
            'warehouseStockReturnCount',
            'totalLowStock',
            'warehouseWise'
        ));
    }
}
