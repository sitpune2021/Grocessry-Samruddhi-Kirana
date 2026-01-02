<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\Product;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{
    public function warehouse_stock_report(Request $request)
    {
        // Fetch filters from GET parameters
        $type          = $request->query('type');          // in, out, transfer
        $fromDate      = $request->query('from_date');     // YYYY-MM-DD
        $toDate        = $request->query('to_date');       // YYYY-MM-DD
        $fromWarehouse = $request->query('from_warehouse');
        $toWarehouse   = $request->query('to_warehouse');
        $download      = $request->query('download');

        // Start query
        $query = DB::table('warehouse_transfers')
            ->where('status', 1)
            ->orderBy('id', 'desc');

        // Date filter
        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        // From Warehouse filter
        if ($fromWarehouse) {
            $query->where('from_warehouse_id', $fromWarehouse);
        }

        // To Warehouse filter
        if ($toWarehouse) {
            $query->where('to_warehouse_id', $toWarehouse);
        }

        // Type filter (optional)
        if ($type) {
            $query->where('type', $type); // adjust column if needed
        }

        $transfers = $query->get();
        $warehouseStock = [];

        foreach ($transfers as $transfer) {
            if ($transfer->quantity <= 0) continue;

            // Warehouse names
            $fromName = DB::table('warehouses')->where('id', $transfer->from_warehouse_id)->value('name') ?? '-';
            $toName   = DB::table('warehouses')->where('id', $transfer->to_warehouse_id)->value('name') ?? '-';

            // Remaining Total Qty
            $remainingQty = DB::table('warehouse_stock')
                ->where('warehouse_id', $transfer->to_warehouse_id)
                ->sum('quantity');

            $warehouseStock[] = [
                'warehouse_from' => $fromName,
                'warehouse_name' => $toName,
                'transfer_in'    => $transfer->quantity,
                'quantity'       => $remainingQty,
                'created_at'     => $transfer->created_at,
                'updated_at'     => $transfer->updated_at,
            ];
        }

        // CSV Download
        if ($download === 'csv') {
            $filename = 'warehouse_stock_report_' . now()->format('Ymd_His') . '.csv';

            return response()->stream(function () use ($warehouseStock) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Warehouse From',
                    'Warehouse To',
                    'Transfer In',
                    'Remaining Qty',
                    'Created Date',
                    'Updated Date'
                ]);

                foreach ($warehouseStock as $row) {
                    fputcsv($file, [
                        $row['warehouse_from'],
                        $row['warehouse_name'],
                        $row['transfer_in'],
                        $row['quantity'],
                        $row['created_at'],
                        $row['updated_at'],
                    ]);
                }

                fclose($file);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return view('reports.warehouse-transfers.warehouse-transfer', compact('warehouseStock'));
    }
    public function stock_movement(Request $request)
    {
        $warehouseId = $request->query('warehouse_id');
        $type        = $request->query('type');
        $fromDate    = $request->query('from_date');
        $toDate      = $request->query('to_date');
        $download    = $request->query('download');

        // 🔹 ORDER BY created_at ASC for running balance
        $query = DB::table('stock_movements')
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc');

        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $movements = $query->get();

        // 🔥 RUNNING BALANCE PER WAREHOUSE
        $runningBalance = [];
        $finalRows = [];

        foreach ($movements as $row) {

            if (!isset($runningBalance[$row->warehouse_id])) {
                $runningBalance[$row->warehouse_id] = 0;
            }

            $runningBalance[$row->warehouse_id] += $row->quantity;

            $finalRows[] = [
                'warehouse_id'   => $row->warehouse_id,
                'type'           => $row->type,
                'quantity'       => $row->quantity,
                'remaining_qty'  => $runningBalance[$row->warehouse_id],
                'created_at'     => $row->created_at,
                'updated_at'     => $row->updated_at,
            ];
        }

        // 🔽 CSV DOWNLOAD
        if ($download === 'csv') {
            $filename = 'stock_movements_' . now()->format('Ymd_His') . '.csv';

            return response()->stream(function () use ($finalRows) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Warehouse',
                    'Type',
                    'Quantity',
                    'Remaining Qty',
                    'Created Date',
                    'Updated Date'
                ]);

                foreach ($finalRows as $row) {
                    $warehouse = DB::table('warehouses')
                        ->where('id', $row['warehouse_id'])
                        ->value('name');

                    fputcsv($file, [
                        $warehouse,
                        strtoupper($row['type']),
                        $row['quantity'],
                        $row['remaining_qty'],
                        $row['created_at'],
                        $row['updated_at'],
                    ]);
                }

                fclose($file);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return view('reports.stock-movements.stock-movement', [
            'movements' => $finalRows
        ]);
    }

    // public function stock_movement(Request $request)
    // {
    //     $warehouseId = $request->query('warehouse_id');
    //     $type        = $request->query('type');
    //     $fromDate    = $request->query('from_date');
    //     $toDate      = $request->query('to_date');
    //     $download    = $request->query('download');

    //     $query = DB::table('stock_movements')
    //         ->orderBy('created_at', 'desc');

    //     // Filter by warehouse
    //     if ($warehouseId) {
    //         $query->where('warehouse_id', $warehouseId);
    //     }

    //     // Filter by type (in / out / transfer)
    //     if ($type) {
    //         $query->where('type', $type);
    //     }

    //     // Date filter
    //     if ($fromDate && $toDate && $fromDate <= $toDate) {
    //         $query->whereBetween('created_at', [
    //             $fromDate . ' 00:00:00',
    //             $toDate . ' 23:59:59'
    //         ]);
    //     }

    //     $movements = $query->get();

    //     // CSV download
    //     if ($download === 'csv') {
    //         $filename = 'stock_movements_' . now()->format('Ymd_His') . '.csv';

    //         return response()->stream(function () use ($movements) {
    //             $file = fopen('php://output', 'w');

    //             fputcsv($file, [
    //                 'Warehouse',
    //                 'Type',
    //                 'Quantity',
    //                 'Created Date'
    //             ]);

    //             foreach ($movements as $row) {
    //                 $warehouse = DB::table('warehouses')
    //                     ->where('id', $row->warehouse_id)
    //                     ->value('name');

    //                 fputcsv($file, [
    //                     $warehouse,
    //                     strtoupper($row->type),
    //                     $row->quantity,
    //                     $row->created_at,
    //                 ]);
    //             }

    //             fclose($file);
    //         }, 200, [
    //             'Content-Type' => 'text/csv',
    //             'Content-Disposition' => "attachment; filename=\"$filename\"",
    //         ]);
    //     }

    //     return view('reports.stock-movements.stock-movement', compact('movements'));
    // }
}
