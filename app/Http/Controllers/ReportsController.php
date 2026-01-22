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
        $fromDate      = $request->query('from_date');
        $toDate        = $request->query('to_date');
        $fromWarehouse = $request->query('from_warehouse');
        $toWarehouse   = $request->query('to_warehouse');
        $download      = $request->query('download');

        $query = DB::table('warehouse_transfers')
        // ->where('status', 2) // ✅ APPROVED
            ->orderBy('id', 'desc');

        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        if ($fromWarehouse) {
            $query->where('approved_by_warehouse_id', $fromWarehouse);
        }

        if ($toWarehouse) {
            $query->where('requested_by_warehouse_id', $toWarehouse);
        }

        $transfers = $query->get();
        $warehouseStock = [];

        foreach ($transfers as $transfer) {

            $fromName = DB::table('warehouses')
                ->where('id', $transfer->approved_by_warehouse_id)
                ->value('name') ?? '-';

            $toName = DB::table('warehouses')
                ->where('id', $transfer->requested_by_warehouse_id)
                ->value('name') ?? '-';

            $remainingQty = DB::table('warehouse_stock')
                ->where('warehouse_id', $transfer->requested_by_warehouse_id)
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

        return view(
            'reports.warehouse-transfers.warehouse-transfer',
            compact('warehouseStock')
        );
    }

    public function stock_movement(Request $request)
    {

        $warehouseId = $request->query('warehouse_id');
        $type        = $request->query('type');
        $fromDate    = $request->query('from_date');
        $toDate      = $request->query('to_date');
        $download    = $request->query('download');

        // ORDER BY created_at ASC for running balance
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

        // RUNNING BALANCE PER WAREHOUSE
        $runningBalance = [];
        $finalRows = [];

        foreach ($movements as $row) 
        {

            $warehouseId = $row->warehouse_id;
            $batchId     = $row->product_batch_id;

            // warehouse initialize
            if (!isset($runningBalance[$warehouseId])) {
                $runningBalance[$warehouseId] = [];
            }

            // batch initialize
            if (!isset($runningBalance[$warehouseId][$batchId])) {
                $runningBalance[$warehouseId][$batchId] = 0;
            }

            // quantity + / -
            $runningBalance[$warehouseId][$batchId] += $row->quantity;

            $finalRows[] = [
                'warehouse_id'     => $warehouseId,
                'product_batch_id' => $batchId,
                'type'             => $row->type,
                'quantity'         => $row->quantity,
                'remaining_qty'    => $runningBalance[$warehouseId][$batchId], // ✅ FIX HERE
                'created_at'       => $row->created_at,
                'updated_at'       => $row->updated_at,
            ];
        }

        // CSV DOWNLOAD
        if ($download === 'csv') 
        {
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

 
}
