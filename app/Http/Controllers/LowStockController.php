<?php

namespace App\Http\Controllers;

use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class LowStockController extends Controller
{

    // LOW STOCK LIST
    public function index()
    {
        $threshold = 100;

        $stocks = WarehouseStock::with([
                'warehouse',
                'product',
                'category',
                'subCategory'
            ])
            ->where('quantity', '<=', $threshold)
            ->whereNull('deleted_at')
            ->orderBy('quantity', 'asc')
            ->get();

        return view('low-stock.index', compact('stocks', 'threshold'));
    }

    // ANALYTICS
    public function analytics()
    {
        $threshold = 100;

        $totalLowStock = WarehouseStock::where('quantity', '<=', $threshold)->count();

        $warehouseWise = WarehouseStock::selectRaw(
                'warehouse_id, COUNT(*) as total'
            )
            ->where('quantity', '<=', $threshold)
            ->groupBy('warehouse_id')
            ->with('warehouse')
            ->get();

        return view('low-stock.analytics', compact(
            'totalLowStock',
            'warehouseWise'
        ));
    }
    
}
