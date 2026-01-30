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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{


    public function index()
    {
        $user = auth()->user()->load('warehouse');
        $warehouse = $user->warehouse;

        Log::info('User logged in and dashboard accessed', [
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'mobile' => $user->mobile,
            'email' => $user->email,
            'warehouse_id' => optional($warehouse)->id,
            'warehouse_name' => optional($warehouse)->name,
            'role_id' => $user->role_id,
            'role_name' => optional($user->role)->name,
            'login_time' => now()->toDateTimeString()
        ]);
       
        // =======================
            // FINAL COUNTS (100% FIXED)
        // =======================

        $categoryCount = Category::count();
        $ProductCount  = Product::count();

        $isAdmin = in_array($user->role_id, [1, 2]); // Super Admin + Master

        if ($isAdmin) {

            // 🔹 ADMIN / MASTER
            $BatchCount = ProductBatch::count();
            $WarehouseCount = Warehouse::count();

            $StockMovementCount = WarehouseStock::count();

            $WarehouseTransferCount = WarehouseTransfer::where('status', 2)->count();

        } else {

            // 🔹 DISTRICT / TALUKA / DC (WAREHOUSE USERS)

            $BatchCount = ProductBatch::where('warehouse_id', $warehouse->id)
                ->count();

            $WarehouseCount = Warehouse::count();

            // ✅ EXACT REQUIRED LOGIC
            $StockMovementCount = WarehouseStock::where('warehouse_id', $warehouse->id)
                ->count();

            $WarehouseTransferCount = WarehouseTransfer::where('approved_by_warehouse_id', $warehouse->id)
                ->where('status', 2)
                ->count();
        }

        // Only show Users count for admin roles (optional)
        $UserCount = in_array($user->role_id, [1, 2])
            ? User::count()
            : 1; // login user only

        // =======================
            // Today Dispatch
        // =======================

        if ($isAdmin) {

            // Admin / Master → all warehouses
            $todayDispatchCount = StockMovement::where('type', 'dispatch')
                ->whereDate('created_at', Carbon::today())
                ->count();

            $todayDispatchQty = StockMovement::where('type', 'dispatch')
                ->whereDate('created_at', Carbon::today())
                ->sum(DB::raw('ABS(quantity)'));

        } else {

            // Login warehouse only
            $todayDispatchCount = StockMovement::where('type', 'dispatch')
                ->where('warehouse_id', $warehouse->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            $todayDispatchQty = StockMovement::where('type', 'dispatch')
                ->where('warehouse_id', $warehouse->id)
                ->whereDate('created_at', Carbon::today())
                ->sum(DB::raw('ABS(quantity)'));
        }


        // ADD STOCK UTILIZATION HERE
        if ($isAdmin) {
            $totalStock = StockMovement::where('type', 'in')->sum('quantity');
            $usedStock  = StockMovement::whereIn('type', ['dispatch','transfer'])
                            ->sum(DB::raw('ABS(quantity)'));
        } else {
            $totalStock = StockMovement::where('warehouse_id', $warehouse->id)
                            ->where('type', 'in')
                            ->sum('quantity');
            $usedStock  = StockMovement::where('warehouse_id', $warehouse->id)
                            ->whereIn('type', ['dispatch','transfer'])
                            ->sum(DB::raw('ABS(quantity)'));
        }

        $stockUtilization = $totalStock > 0
            ? round(($usedStock / $totalStock) * 100, 1)
            : 0;
         

        // =======================
        // IN vs OUT Trend (last 7 days)
        // =======================

        $startDate = Carbon::today()->subDays(6); // last 7 days
        $endDate = Carbon::today();

        if ($isAdmin) {
            $inTrend = StockMovement::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(quantity) as total_in')
                        )
                        ->where('type', 'in')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date')
                        ->pluck('total_in', 'date'); // key = date, value = total

            $outTrend = StockMovement::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(ABS(quantity)) as total_out')
                        )
                        ->whereIn('type', ['dispatch','transfer'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date')
                        ->pluck('total_out', 'date');

        } else {
            $inTrend = StockMovement::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(quantity) as total_in')
                        )
                        ->where('warehouse_id', $warehouse->id)
                        ->where('type', 'in')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date')
                        ->pluck('total_in', 'date');

            $outTrend = StockMovement::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(ABS(quantity)) as total_out')
                        )
                        ->where('warehouse_id', $warehouse->id)
                        ->whereIn('type', ['dispatch','transfer'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy(DB::raw('DATE(created_at)'))
                        ->orderBy('date')
                        ->pluck('total_out', 'date');
        }

        // Generate labels for last 7 days
        $trendLabels = [];
        for ($i = 0; $i < 7; $i++) {
            $trendLabels[] = Carbon::today()->subDays(6 - $i)->format('d M');
        }

        $trendIn = [];
        $trendOut = [];

        foreach ($trendLabels as $label) {
            $date = Carbon::createFromFormat('d M', $label)->format('Y-m-d');
            $trendIn[] = $inTrend[$date] ?? 0;
            $trendOut[] = $outTrend[$date] ?? 0;
        }
        

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
            // Pending Warehouse Transfers (status = 0)
        // =======================

        // Total Pending Requests
        //$pendingTransferCount = WarehouseTransfer::where('status', 0)->count();
        if (in_array($user->role_id, [1, 2])) {

            $pendingTransferCount = WarehouseTransfer::where('status', 0)->count();

        } else {

            if ($warehouse->type === 'district') {

                $warehouseIds = Warehouse::where('district_id', $warehouse->district_id)
                    ->pluck('id');

                $pendingTransferCount = WarehouseTransfer::where('status', 0)
                    ->where(function ($q) use ($warehouseIds) {
                        $q->whereIn('requested_by_warehouse_id', $warehouseIds)
                        ->orWhereIn('approved_by_warehouse_id', $warehouseIds);
                    })->count();

            } else {

                $pendingTransferCount = WarehouseTransfer::where('status', 0)
                    ->where(function ($q) use ($warehouse) {
                        $q->where('requested_by_warehouse_id', $warehouse->id)
                        ->orWhere('approved_by_warehouse_id', $warehouse->id);
                    })->count();
            }
        }

        // Requested -> Approved warehouse wise analytics
        $pendingTransferAnalytics = WarehouseTransfer::selectRaw('
                requested_by_warehouse_id,
                approved_by_warehouse_id,
                COUNT(*) as total
            ')
            ->where('status', 0)
            ->groupBy('requested_by_warehouse_id', 'approved_by_warehouse_id')
            ->with(['requestedByWarehouse', 'approvedByWarehouse'])
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
            'warehouseWise',
            'pendingTransferCount',
            'pendingTransferAnalytics',
            'todayDispatchCount', 
            'todayDispatchQty',
            'stockUtilization',   
            'totalStock',        
            'usedStock' ,
            'trendLabels',      
            'trendIn',          
            'trendOut'                  
        ));
    }


}
