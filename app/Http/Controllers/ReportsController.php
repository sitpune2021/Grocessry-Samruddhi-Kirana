<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Product;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{


   public function warehouse_stock_report(Request $request)
{
    $warehouses = DB::table('warehouses')->get();

    // Fetch once (IMPORTANT)
    $allTransfers = DB::table('warehouse_transfers')->get();
    $allStocks = DB::table('stock_movements')->get();

    $warehouseStock = [];

    foreach ($warehouses as $wh) {

        // Stock In / Out
        $stockIn = $allStocks
            ->where('warehouse_id', $wh->id)
            ->where('type', 'in')
            ->sum('quantity');

        $stockOut = $allStocks
            ->where('warehouse_id', $wh->id)
            ->where('type', 'out')
            ->sum('quantity');

        // Transfer In records
        $transferInRecords = $allTransfers
            ->where('to_warehouse_id', $wh->id);

        $transferIn = $transferInRecords->sum('quantity');

        $transferOut = $allTransfers
            ->where('from_warehouse_id', $wh->id)
            ->sum('quantity');

        // Warehouse From (unique names)
        $warehouseFrom = $transferInRecords
            ->pluck('from_warehouse_id')
            ->unique()
            ->map(function ($id) {
                return DB::table('warehouses')->where('id', $id)->value('name');
            })
            ->filter()
            ->implode(', ');

        $warehouseStock[$wh->id] = [
            'warehouse_from' => $warehouseFrom ?: '-',
            'warehouse_name' => $wh->name,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'transfer_in' => $transferIn,
            'transfer_out' => $transferOut,
            'remaining' => ($stockIn + $transferIn) - ($stockOut + $transferOut),
        ];
    }

    return view('reports.warehouse-stock', compact('warehouseStock'));
}


    // public function warehouse_stock_report(Request $request)
    // {
    //     $warehouses = Warehouse::all();
    //     $categories = Category::all();
    //     $products   = Product::all();

    //     // Fetch all stock movements with relations
    //     $stocks = StockMovement::with(['warehouse', 'product', 'category'])
    //         ->when($request->type, fn($q) => $q->where('type', $request->type))
    //         ->orderBy('created_at', 'desc')
    //         ->get();
    //     // get all, we'll calculate per warehouse

    //     // Calculate remaining stock per warehouse
    //     $warehouseStock = [];
    //     foreach ($warehouses as $wh) {
    //         $in = $stocks->where('type', 'in')->where('warehouse_id', $wh->id)->sum('quantity');
    //         $out = $stocks->where('type', 'out')->where('warehouse_id', $wh->id)->sum('quantity');
    //         $transferIn = $stocks->where('type', 'transfer')->where('to_warehouse_id', $wh->id)->sum('quantity');
    //         $transferOut = $stocks->where('type', 'transfer')->where('from_warehouse_id', $wh->id)->sum('quantity');

    //         $warehouseStock[$wh->id] = [
    //             'warehouse_name' => $wh->name,
    //             'stock_in'       => $in + $transferIn,
    //             'stock_out'      => $out + $transferOut,
    //             'remaining'      => ($in + $transferIn) - ($out + $transferOut),
    //         ];
    //     }

    //     return view('reports.warehouse-stock', compact(
    //         'stocks',
    //         'warehouses',
    //         'categories',
    //         'products',
    //         'warehouseStock'
    //     ));
    // }
}
