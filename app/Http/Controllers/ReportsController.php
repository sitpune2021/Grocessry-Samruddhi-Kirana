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
use Carbon\Carbon;

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

            $productName = DB::table('products')
                ->where('id', $transfer->product_id)
                ->value('name') ?? '-';

            /* 🔑 ADD START (no logic changed) */
            $requestStock = $transfer->quantity;

            // $requestStock = DB::table('stock_movements')
            //     ->where('reference_id', $transfer->id)
            //     ->where('type', 'transfer')
            //     ->sum('quantity');

            $dispatchStock = DB::table('warehouse_stock')
                ->where('warehouse_id', $transfer->requested_by_warehouse_id)
                ->where('product_id', $transfer->product_id)
                ->orderBy('id', 'desc')   // 🔑 latest row only
                ->value('quantity');

            /* 🔑 ADD END */

            $warehouseStock[] = [
                'warehouse_from' => $fromName,
                'warehouse_name' => $toName,

                'transfer_in'    => $transfer->quantity,

                // ✅ real quantities from stock_movements
                'request_stock'  => $requestStock,
                'dispatch_stock' => $dispatchStock,

                'product_name'   => $productName,
                'quantity'       => $remainingQty,
                'created_at'     => $transfer->created_at,
                'updated_at'     => $transfer->updated_at,
            ];
        }


        if ($download === 'csv') {

            $filename = 'warehouse_stock_report_' . date('Ymd_His') . '.csv';

            $headers = [
                "Content-Type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ];

            $callback = function () use ($warehouseStock) {
                $file = fopen('php://output', 'w');

                // CSV Header
                fputcsv($file, [
                    'From Warehouse',
                    'To Warehouse',
                    'request_stock',
                    'dispatch_stock',
                    'product_name',
                    'Created At',
                    'Updated At'
                ]);

                foreach ($warehouseStock as $row) {
                    fputcsv($file, [
                        $row['warehouse_from'] ?? '-',
                        $row['warehouse_name'] ?? '-',
                        $row['request_stock'] ?? 0,
                        $row['dispatch_stock'] ?? 0,
                        $row['product_name'] ?? 0,
                        $row['created_at'] ?? '',
                        $row['updated_at'] ?? '',
                    ]);
                }


                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // 🔹 Normal page load
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

        // 🔒 DO NOT CHANGE ORDER (needed for running balance)
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

        // ✅ RUNNING BALANCE
        $runningBalance = [];
        $finalRows = [];

        foreach ($movements as $row) {

            $warehouseId = $row->warehouse_id;
            $batchId     = $row->product_batch_id;

            // warehouse init
            if (!isset($runningBalance[$warehouseId])) {
                $runningBalance[$warehouseId] = [];
            }

            // batch init
            if (!isset($runningBalance[$warehouseId][$batchId])) {
                $runningBalance[$warehouseId][$batchId] = 0;
            }

            // qty +/- 
            $runningBalance[$warehouseId][$batchId] += $row->quantity;

            // ✅ PRODUCT NAME (FIX)
            $productName = DB::table('product_batches as pb')
                ->join('products as p', 'p.id', '=', 'pb.product_id')
                ->where('pb.id', $batchId)
                ->value('p.name');

            $finalRows[] = [
                'warehouse_id'     => $warehouseId,
                'product_batch_id' => $batchId,
                'product_name'     => $productName ?? '-', // 🔥 FIXED
                'type'             => $row->type,
                'quantity'         => $row->quantity,
                'remaining_qty'    => $runningBalance[$warehouseId][$batchId],
                'created_at'       => $row->created_at,
                'updated_at'       => $row->updated_at,
            ];
        }

        if ($download === 'csv') {

            $filename = 'stock_movements_' . now()->format('Ymd_His') . '.csv';

            return response()->stream(function () use ($finalRows) {

                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Warehouse',
                    'Product',
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
                        $warehouse ?? '-',
                        $row['product_name'],
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

    public function stockReturnReport(Request $request)
    {
        $fromDate    = $request->query('from_date');
        $toDate      = $request->query('to_date');
        $warehouseId = $request->query('warehouse_id');
        $download    = $request->query('download');

        $query = DB::table('warehouse_stock_returns as r')
            ->join('warehouse_stock_return_items as i', 'i.stock_return_id', '=', 'r.id')
            ->leftJoin('warehouses as fw', 'fw.id', '=', 'r.from_warehouse_id')
            ->leftJoin('warehouses as tw', 'tw.id', '=', 'r.to_warehouse_id')
            ->leftJoin('products as p', 'p.id', '=', 'i.product_id')
            ->select([
                'r.return_number',
                'fw.name as from_warehouse',
                'tw.name as to_warehouse',
                'p.name as product_name',
                'i.batch_no',
                'i.return_qty',
                'i.received_qty',
                // 'i.damaged_qty',
                // 'i.condition',
                'r.status',
                'r.created_at',
                'r.updated_at',
            ])
            ->orderBy('r.id', 'desc');

        if ($warehouseId) {
            $query->where(function ($q) use ($warehouseId) {
                $q->where('r.from_warehouse_id', $warehouseId)
                    ->orWhere('r.to_warehouse_id', $warehouseId);
            });
        }

        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('r.created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $returns = $query->get();


        if ($download === 'csv') {

            $filename = 'stock_return_report_' . date('Ymd_His') . '.csv';

            $headers = [
                "Content-Type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ];

            $callback = function () use ($returns) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Return No',
                    'From Warehouse',
                    'To Warehouse',
                    'Product',
                    'Batch No',
                    'Return Qty',
                    'Received Qty',
                    // 'Damaged Qty',
                    // 'Condition',
                    'Status',
                    'Created Date',
                ]);

                foreach ($returns as $row) {
                    fputcsv($file, [
                        $row->return_number,
                        $row->from_warehouse ?? '-',
                        $row->to_warehouse ?? '-',
                        $row->product_name ?? '-',
                        $row->batch_no ?? '-',
                        $row->return_qty,
                        // $row->received_qty,
                        // $row->damaged_qty,
                        $row->condition,
                        $row->status,
                        \Carbon\Carbon::parse($row->created_at)->format('d-m-Y'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('reports.warehouse-stock-returns.warehouse-stock-returns', compact('returns'));
    }


    public function pos_report(Request $request)
    {
        $fromDate    = $request->query('from_date');
        $toDate      = $request->query('to_date');
        $warehouseId = $request->query('warehouse_id');
        $download    = $request->query('download');

        $query = DB::table('orders as o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->leftJoin('products as p', 'p.id', '=', 'oi.product_id')
            ->leftJoin('warehouses as w', 'w.id', '=', 'o.warehouse_id')
            ->where('o.channel', 'pos')          // POS only
            ->where('o.order_type', 'walkin')    // ✅ WALK-IN ONLY
            ->select([
                'o.order_number',
                'w.name as warehouse_name',
                'o.payment_method',
                'o.payment_status',
                'p.name as product_name',
                'oi.quantity',
                'oi.price',
                'oi.total as line_total',
                'o.total_amount',
                'o.created_at',
            ])
            ->orderBy('o.id', 'desc');

        if ($warehouseId) {
            $query->where('o.warehouse_id', $warehouseId);
        }

        if ($fromDate && $toDate && $fromDate <= $toDate) {
            $query->whereBetween('o.created_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $rows = $query->get();

        if ($download === 'csv') {

            $filename = 'pos_walkin_report_' . date('Ymd_His') . '.csv';

            $headers = [
                "Content-Type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
            ];

            $callback = function () use ($rows) {
                $file = fopen('php://output', 'w');

                fputcsv($file, [
                    'Order No',
                    'Warehouse',
                    'Product',
                    'Quantity',
                    'Price',
                    'Line Total',
                    'Order Total',
                    'Payment Method',
                    'Payment Status',
                    'Date'
                ]);

                foreach ($rows as $row) {
                    fputcsv($file, [
                        $row->order_number,
                        $row->warehouse_name ?? '-',
                        $row->product_name ?? '-',
                        $row->quantity,
                        $row->price,
                        $row->line_total,
                        $row->total_amount,
                        $row->payment_method ?? '-',
                        $row->payment_status,
                        Carbon::parse($row->created_at)->format('d-m-Y'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('reports.pos-report.pos-report', compact('rows'));
    }
}
