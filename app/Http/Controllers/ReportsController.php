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
        if ($request->isMethod('post')) {
            session([
                'ws_type'      => $request->type,
                'ws_from_date' => $request->from_date,
                'ws_to_date'   => $request->to_date,
            ]);
        }

        $type     = session('ws_type');
        $fromDate = session('ws_from_date');
        $toDate   = session('ws_to_date');
        $download = $request->download;

        $query = DB::table('warehouse_transfers')
            ->where('status', 1)
            ->orderBy('id', 'desc');

        // ✅ Date filter (safe)
        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $transfers = $query->get();
        $warehouseStock = [];

        foreach ($transfers as $transfer) {

            if ($transfer->quantity <= 0) continue;

            $fromName = DB::table('warehouses')->where('id', $transfer->from_warehouse_id)->value('name') ?? '-';
            $toName   = DB::table('warehouses')->where('id', $transfer->to_warehouse_id)->value('name') ?? '-';

            $warehouseStock[] = [
                'warehouse_from' => $fromName,
                'warehouse_name' => $toName,
                'stock_in'       => 0,
                'stock_out'      => 0,
                'transfer_in'    => $transfer->quantity,
                'transfer_out'   => 0,
                'remaining'      => $transfer->quantity,
                'created_at'     => $transfer->created_at,
                'updated_at'     => $transfer->updated_at,
            ];
        }

        // ✅ CSV DOWNLOAD (still works)
        if ($download === 'csv') {
            $filename = 'warehouse_stock_report_' . now()->format('Ymd_His') . '.csv';

            return response()->stream(function () use ($warehouseStock) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Warehouse From',
                    'Warehouse To',
                    'Stock In',
                    'Stock Out',
                    'Transfer In',
                    'Transfer Out',
                    'Remaining Qty',
                    'Created Date',
                    'Updated Date'
                ]);

                foreach ($warehouseStock as $row) {
                    fputcsv($file, [
                        $row['warehouse_from'],
                        $row['warehouse_name'],
                        $row['stock_in'],
                        $row['stock_out'],
                        $row['transfer_in'],
                        $row['transfer_out'],
                        $row['remaining'],
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

        return view('reports.warehouse-stock', compact('warehouseStock'));
    }





    // public function warehouse_stock_report(Request $request)
    // {
    //     $fromDate = $request->from_date;
    //     $toDate   = $request->to_date;

    //     // Fetch all transfers, optionally filter by date
    //     $transferQuery = DB::table('warehouse_transfers')
    //         ->whereNull('deleted_at')
    //         ->orderBy('created_at', 'asc');

    //     if ($fromDate && $toDate) {
    //         $transferQuery->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
    //     }

    //     $allTransfers = $transferQuery->get();

    //     $warehouseStock = [];

    //     foreach ($allTransfers as $transfer) {

    //         $fromWarehouseName = DB::table('warehouses')
    //             ->where('id', $transfer->from_warehouse_id)
    //             ->value('name') ?? '-';

    //         $toWarehouseName = DB::table('warehouses')
    //             ->where('id', $transfer->to_warehouse_id)
    //             ->value('name') ?? '-';

    //         $warehouseStock[] = [
    //             'warehouse_from' => $fromWarehouseName,
    //             'warehouse_name' => $toWarehouseName,
    //             'stock_in'       => 0, // keep zero if you are not using
    //             'stock_out'      => 0, // keep zero
    //             'transfer_in'    => $transfer->quantity,
    //             'transfer_out'   => 0, // optional
    //             'remaining'      => $transfer->quantity, // net quantity per row
    //             'created_at'     => $transfer->created_at,
    //         ];
    //     }

    //     return view('reports.warehouse-stock', compact('warehouseStock'));
    // }

    // public function warehouse_stock_report(Request $request)
    // {
    //     $fromDate = $request->from_date;
    //     $toDate   = $request->to_date;

    //     // Fetch all transfers, optionally filter by date
    //     $transferQuery = DB::table('warehouse_transfers')
    //         ->whereNull('deleted_at')
    //         ->orderBy('created_at', 'asc');

    //     if ($fromDate && $toDate) {
    //         $transferQuery->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
    //     }

    //     $allTransfers = $transferQuery->get();

    //     $warehouseStock = [];

    //     foreach ($allTransfers as $transfer) {

    //         $fromWarehouseName = DB::table('warehouses')
    //             ->where('id', $transfer->from_warehouse_id)
    //             ->value('name') ?? '-';

    //         $toWarehouseName = DB::table('warehouses')
    //             ->where('id', $transfer->to_warehouse_id)
    //             ->value('name') ?? '-';

    //         $warehouseStock[] = [
    //             'warehouse_from' => $fromWarehouseName,
    //             'warehouse_name' => $toWarehouseName,
    //             'stock_in'       => 0, // optional: if you track stock in separately
    //             'stock_out'      => 0, // optional
    //             'transfer_in'    => $transfer->quantity,
    //             'transfer_out'   => 0, // optional: you can calculate if needed
    //             'remaining'      => $transfer->quantity, // if you want net remaining per row
    //             'created_at'     => $transfer->created_at,
    //         ];
    //     }

    //     return view('reports.warehouse-stock', compact('warehouseStock'));
    // }




}
