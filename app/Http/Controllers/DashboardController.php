<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Product;
use App\Models\Batch;
use App\Models\ProductBatch;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\GroceryShop;
use App\Models\User;

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
            'shops'
        ));
    }
}
