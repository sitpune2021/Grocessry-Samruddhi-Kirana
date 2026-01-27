<?php

namespace App\Http\Controllers;

use App\Models\WarehouseStock;
use Illuminate\Http\Request;

class LowStockController extends Controller
{

    // LOW STOCK LIST
    public function index(Request $request)
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

        /* ===============================
       ✅ CSV DOWNLOAD
    ================================*/
        if ($request->query('download') === 'csv') {

            $filename = 'low_stock_report_' . date('Ymd_His') . '.csv';

            $headers = [
                "Content-Type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ];

            $callback = function () use ($stocks) {
                $file = fopen('php://output', 'w');

                // CSV Header
                fputcsv($file, [
                    'SR NO',
                    'Warehouse',
                    'Product',
                    'Category',
                    'Quantity',
                    'Status'
                ]);

                foreach ($stocks as $index => $stock) {
                    fputcsv($file, [
                        $index + 1,
                        $stock->warehouse->name ?? '-',
                        $stock->product->name ?? '-',
                        $stock->category->name ?? '-',
                        $stock->quantity,
                        'LOW STOCK'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // 🔹 Normal page view
        return view('low-stock.index', compact('stocks', 'threshold'));
    }

    // public function index()
    // {
    //     $threshold = 100;

    //     $stocks = WarehouseStock::with([
    //             'warehouse',
    //             'product',
    //             'category',
    //             'subCategory'
    //         ])
    //         ->where('quantity', '<=', $threshold)
    //         ->whereNull('deleted_at')
    //         ->orderBy('quantity', 'asc')
    //         ->get();

    //     return view('low-stock.index', compact('stocks', 'threshold'));
    // }

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
