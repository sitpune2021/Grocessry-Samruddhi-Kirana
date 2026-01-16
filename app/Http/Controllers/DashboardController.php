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
        // =====================
        // Basic Counts
        // =====================
        $categoryCount           = Category::count();
        $ProductCount            = Product::count();
        $BatchCount              = Batch::count();
        $WarehouseCount          = Warehouse::count();
        $StockMovementCount      = WarehouseStock::count();
        $WarehouseTransferCount  = WarehouseTransfer::count();
        $UserCount               = User::count();

        // =====================
        // Warehouse / Shop Lists
        // =====================
        $warehouseDistrict = Warehouse::whereNotNull('district_id')
            ->pluck('name');

        $warehouseTaluka = Warehouse::whereNotNull('taluka_id')
            ->pluck('name');

        $shops = GroceryShop::where('status', 'active')
            ->pluck('shop_name');

        // =====================
        // Expiry Alert Logic (Dashboard Only)
        // =====================
        $today = now();

        // Expired batches (only if stock available)
        $expiredCount = ProductBatch::where('quantity', '>', 0)
            ->whereDate('expiry_date', '<', $today)
            ->count();

        // Expiring in next 7 days
        $expiringSoonCount = ProductBatch::where('quantity', '>', 0)
            ->whereBetween('expiry_date', [
                $today,
                $today->copy()->addDays(7)
            ])
            ->count();


        // =====================
        // 🔁 RETURN COUNTS (NEW)
        // =====================
        $user = auth()->user()->load('warehouse');
        $warehouse = $user->warehouse;

        $warehouseStockReturnCount = null;
        $customerOrderReturnCount  = null;

        if ($warehouse) {

            // TALUKA → Only returns raised by taluka
            if ($warehouse->type === 'taluka') {
                $warehouseStockReturnCount =
                    WarehouseStockReturn::where('from_warehouse_id', $warehouse->id)
                    ->where('status', '!=', 'received')
                    ->count();
            }

            // DISTRICT → Taluka + District
            if ($warehouse->type === 'district') {
                $warehouseStockReturnCount =
                    WarehouseStockReturn::where(function ($q) use ($warehouse) {
                        $q->where('from_warehouse_id', $warehouse->id)
                            ->orWhere('to_warehouse_id', $warehouse->id);
                    })
                    ->where('status', '!=', 'received')
                    ->count();
            }

            // MASTER → All warehouse returns
            // if ($warehouse->type === 'master') {
            //     $warehouseStockReturnCount =
            //         WarehouseStockReturn::where('status', '!=', 'received')->count();

            //     $customerOrderReturnCount =
            //         CustomerOrderReturn::where('status', 'pending')->count();
            // }
        }

        // ADMIN → See everything
        // if ($user->role->name === 'Super Admin') {
        //     $warehouseStockReturnCount =
        //         WarehouseStockReturn::where('status', '!=', 'received')->count();

        //     $customerOrderReturnCount =
        //         CustomerOrderReturn::where('status', 'pending')->count();
        // }


        // =====================
        // Send Data to Dashboard
        // =====================
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
            'customerOrderReturnCount'
        ));
    }
}
