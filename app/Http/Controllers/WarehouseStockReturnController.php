<?php

namespace App\Http\Controllers;

use App\Exceptions\StockDispatchException;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\TransferChallan;
use App\Models\TransferChallanItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseStockReturn;
use App\Models\WarehouseStockReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class WarehouseStockReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $user = auth()->user();
        $warehouseId = $user->warehouse_id;
        $userWarehouseType = $user->warehouse->type ?? null;

        $returns = WarehouseStockReturn::with([
            'fromWarehouse',
            'toWarehouse',
            'WarehouseStockReturnItem',
            'creator.role'
        ]);
        if ($userWarehouseType === 'taluka') {
            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId) // Taluka created
                    ->orWhere('to_warehouse_id', $warehouseId); // Taluka receiving
            });
        }

        if ($userWarehouseType === 'district') {
            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                    ->orWhere('to_warehouse_id', $warehouseId);
            });
        }

        if ($userWarehouseType === 'distribution_center') {

            $returns->where(function ($q) use ($warehouseId) {
                $q->where('from_warehouse_id', $warehouseId)
                    ->orWhere('to_warehouse_id', $warehouseId);
            });
        }


        if ($userWarehouseType === 'master') {
            $returns->where('to_warehouse_id', $warehouseId);
        }

        $returns = $returns->latest()->paginate(10);

        return view(
            'menus.warehouse-stock-return.stock-return-index',
            compact('returns', 'userWarehouseType')
        );
    }

    // Raise stock return request
    public function create()
    {
        $mode = "add";
        $user = User::with('warehouse')->findOrFail(auth()->id());

        $fromWarehouse = $user->warehouse;

        $fromWarehouseId = $fromWarehouse->id ?? null;
        /**
         * FILTER TO WAREHOUSE BASED ON LEVEL
         */
        if ($fromWarehouse?->type === 'distribution_center' || $fromWarehouse?->type === 'taluka' || $fromWarehouse?->type === 'district') {
            $warehouses = Warehouse::where('type', 'master')->get();
        } else {
            // Master → No return allowed
            $warehouses = collect();
        }

        // Fetch only received challans that came TO this warehouse
        $challans = TransferChallan::where('to_warehouse_id', $fromWarehouseId)
            ->where('status', 'received')
            ->with(['fromWarehouse'])
            ->orderBy('transfer_date', 'desc')
            ->get();

        /**
         * AVAILABLE STOCK IN LOGGED-IN WAREHOUSE
         */
        $warehouseStocks = ProductBatch::with('product')
            ->where('warehouse_id', $fromWarehouseId)
            ->where('is_blocked', 0)
            ->get();

        return view('menus.warehouse-stock-return.stock-return', compact(
            'warehouses',
            'user',
            'warehouseStocks',
            'challans',
            'mode'
        ));
    }
    public function challanProducts($challanId)
    {
        $items = TransferChallanItem::join(
            'product_batches',
            'product_batches.id',
            '=',
            'transfer_challan_items.batch_no' // batch_no holds batch ID
        )
            ->join('products', 'products.id', '=', 'transfer_challan_items.product_id')
            ->where('transfer_challan_items.transfer_challan_id', $challanId)
            ->selectRaw('
            transfer_challan_items.product_id,
            products.name as product_name,
            transfer_challan_items.batch_no as batch_id,
            product_batches.batch_no as batch_no,
            SUM(transfer_challan_items.quantity) as challan_qty
        ')
            ->groupBy(
                'transfer_challan_items.product_id',
                'products.name',
                'transfer_challan_items.batch_no',
                'product_batches.batch_no'
            )
            ->get();

        return response()->json($items);
    }



    public function store(Request $request)
    {
        Log::info('Stock return request initiated', [
            'user_id' => auth()->id(),
            'warehouse_id' => auth()->user()->warehouse_id,
            'payload' => $request->all(),
        ]);

        $request->validate([
            'transfer_challan_id' => 'required|exists:transfer_challans,id',
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_no' => 'required|integer|exists:product_batches,id',
            'items.*.return_qty' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $user = auth()->user();
            $fromWarehouse = $user->warehouse;

            if ($fromWarehouse->type === 'master') {
                Log::warning('Master warehouse attempted stock return', [
                    'user_id' => $user->id,
                    'warehouse_id' => $fromWarehouse->id,
                ]);
                abort(403);
            }

            // Validate challan ownership
            $challan = TransferChallan::where('id', $request->transfer_challan_id)
                ->where('status', 'received')
                ->where('to_warehouse_id', $fromWarehouse->id)
                ->firstOrFail();

            Log::info('Validated transfer challan for return', [
                'challan_id' => $challan->id,
                'challan_no' => $challan->challan_no,
                'from_warehouse_id' => $challan->from_warehouse_id,
                'to_warehouse_id' => $challan->to_warehouse_id,
            ]);

            $return = WarehouseStockReturn::create([
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'status' => 'draft',
                'return_reason' => $request->return_reason,
                'created_by' => $user->id,
            ]);

            Log::info('Stock return record created', [
                'stock_return_id' => $return->id,
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $request->to_warehouse_id,
            ]);

            foreach ($request->items as $index => $item) {

                $batch = ProductBatch::where('batch_no', function ($q) use ($item) {
                    $q->select('batch_no')
                        ->from('product_batches')
                        ->where('id', $item['batch_no']);
                })
                    ->where('product_id', $item['product_id'])
                    ->where('warehouse_id', $fromWarehouse->id)
                    ->first();

                if (!$batch) {
                    abort(422, 'Invalid batch selected');
                }

                $available = $batch->quantity;


                Log::info('Checking batch stock', [
                    'stock_return_id' => $return->id,
                    'row' => $index,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_no'],
                    'available_stock' => $available,
                    'requested_return_qty' => $item['return_qty'],
                ]);

                if ($available === null) {
                    Log::error('Invalid batch selected for return', [
                        'batch_id' => $item['batch_no'],
                        'warehouse_id' => $fromWarehouse->id,
                    ]);
                    abort(422, 'Invalid batch selected');
                }

                // Challan qty (PER BATCH)
                $challanQty = TransferChallanItem::where('transfer_challan_id', $request->transfer_challan_id)
                    ->where('product_id', $item['product_id'])
                    ->where('batch_no', $item['batch_no'])
                    ->sum('quantity');

                Log::info('Challan quantity check', [
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_no'],
                    'challan_qty' => $challanQty,
                ]);

                if ($item['return_qty'] > $challanQty) {
                    Log::warning('Return qty exceeds challan qty', [
                        'product_id' => $item['product_id'],
                        'batch_id' => $item['batch_no'],
                        'return_qty' => $item['return_qty'],
                        'challan_qty' => $challanQty,
                    ]);
                    abort(422, 'Return quantity exceeds challan quantity for this batch');
                }

                if ($item['return_qty'] > $available) {
                    Log::warning('Return qty exceeds available batch stock', [
                        'product_id' => $item['product_id'],
                        'batch_id' => $item['batch_no'],
                        'return_qty' => $item['return_qty'],
                        'available_stock' => $available,
                    ]);
                    abort(422, 'Return quantity exceeds available batch stock');
                }

                WarehouseStockReturnItem::create([
                    'stock_return_id' => $return->id,
                    'batch_no' => $item['batch_no'],
                    'product_id' => $item['product_id'],
                    'return_qty' => $item['return_qty'],
                ]);

                Log::info('Stock return item added', [
                    'stock_return_id' => $return->id,
                    'product_id' => $item['product_id'],
                    'batch_id' => $item['batch_no'],
                    'return_qty' => $item['return_qty'],
                ]);
            }
        });

        Log::info('Stock return process completed successfully', [
            'user_id' => auth()->id(),
            'warehouse_id' => auth()->user()->warehouse_id,
        ]);

        return redirect()->route('stock-returns.index')
            ->with('success', 'Stock return created successfully');
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $return = WarehouseStockReturn::with([
            'items.product',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view('stock_returns.show', compact('return'));
    }


    public function approveByMaster($id)
    {
        DB::transaction(function () use ($id) {

            $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);
            $user = auth()->user();

            if (
                $return->status !== 'draft' ||
                $user->warehouse->type !== 'master' ||
                $user->warehouse_id !== $return->to_warehouse_id
            ) {
                abort(403);
            }

            $return->update([
                'status'      => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Return approved by Master');
    }

    public function dispatch($id)
    {
        try {

            Log::info('Dispatch request initiated', [
                'return_id' => $id,
                'user_id' => auth()->id(),
            ]);

            DB::transaction(function () use ($id) {

                $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);
                $user = auth()->user();

                // Master cannot dispatch
                if (optional($user->warehouse)->type === 'master') {
                    throw new StockDispatchException('Master warehouse cannot dispatch stock');
                }

                foreach ($return->WarehouseStockReturnItem as $item) {

                    // Resolve batch_no string
                    $batchNo = ProductBatch::where('id', $item->batch_no)->value('batch_no');

                    if (!$batchNo) {
                        throw new StockDispatchException('Original batch not found');
                    }

                    // Find batch in FROM warehouse
                    $batch = ProductBatch::where('batch_no', $batchNo)
                        ->where('product_id', $item->product_id)
                        ->where('warehouse_id', $return->from_warehouse_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$batch) {
                        throw new StockDispatchException('Batch not found in source warehouse');
                    }

                    if ($batch->quantity < $item->return_qty) {
                        throw new StockDispatchException('Insufficient batch stock');
                    }

                    $batch->decrement('quantity', $item->return_qty);

                    $warehouseStock = WarehouseStock::where('warehouse_id', $return->from_warehouse_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (!$warehouseStock || $warehouseStock->quantity < $item->return_qty) {
                        throw new StockDispatchException('Insufficient warehouse stock');
                    }

                    $warehouseStock->decrement('quantity', $item->return_qty);

                    StockMovement::create([
                        'warehouse_id' => $return->from_warehouse_id,
                        'product_batch_id' => $batch->id,
                        'quantity' => -$item->return_qty,
                        'type' => 'return',
                        'reference_id' => $return->id,
                        'created_by' => auth()->id(),
                    ]);
                }

                $return->update([
                    'status' => 'dispatched',
                    'dispatched_at' => now(),
                ]);
            });

            return back()->with('success', 'Stock dispatched successfully');
        } catch (StockDispatchException $e) {

            Log::warning('Stock dispatch failed', [
                'return_id' => $id,
                'message' => $e->getMessage(),
            ]);

            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {

            Log::error('Unexpected dispatch error', [
                'return_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Something went wrong while dispatching stock');
        }
    }
    public function receiveAtMaster($id)
    {
        DB::transaction(function () use ($id) {

            $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);
            $user = auth()->user();

            if (
                $return->status !== 'dispatched' ||
                $user->warehouse->type !== 'master' ||
                $user->warehouse_id !== $return->to_warehouse_id
            ) {
                abort(403);
            }

            foreach ($return->WarehouseStockReturnItem as $item) {

                // If no damage handling UI yet, assume full quantity received
                $receivedQty = $item->return_qty;
                $damagedQty  = 0;

                // UPDATE RETURN ITEM
                $item->update([
                    'received_qty' => $receivedQty,
                    'damaged_qty'  => $damagedQty,
                ]);
                StockMovement::create([
                    'warehouse_id' => $return->to_warehouse_id,
                    'product_batch_id' => $item->batch_no,
                    'quantity' => $item->return_qty,
                    'type' => 'return',
                    // 'reference_type' => 'RETURN_TO_MASTER',
                    'reference_id' => $return->id,
                    'created_by' => $user->id,
                ]);
            }

            $return->update([
                'status'      => 'received',
                'received_by' => $user->id,
                'received_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock received at Master');
    }


    public function downloadPdf(string $id)
    {
        $return = WarehouseStockReturn::with([
            'WarehouseStockReturnItem',
            'fromWarehouse',
            'toWarehouse',
            'creator',
            'approvedBy',
            'receivedBy'
        ])->findOrFail($id);

        return view(
            'menus.warehouse-stock-return.challan-draft',
            compact('return')
        );
    }
}
