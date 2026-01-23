<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
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
            'mode'
        ));
    }

    public function store(Request $request)
    {

        $request->validate([
            'to_warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.return_qty' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request) {

            $user = auth()->user();
            $fromWarehouse = $user->warehouse;

            // Safety: master cannot return
            if ($fromWarehouse->type === 'master') {
                abort(403);
            }

            // Create return
            $return = WarehouseStockReturn::create([
                'from_warehouse_id' => $fromWarehouse->id,
                'to_warehouse_id' => $request->to_warehouse_id, // MASTER
                'status' => 'draft',
                'return_reason' =>  $request->return_reason,
                'created_by' => $user->id,
            ]);

            foreach ($request->items as $item) {

                // Prevent over-return
                $available = ProductBatch::where([
                    'warehouse_id' => $fromWarehouse->id,
                    'product_id' => $item['product_id'],
                ])->sum('quantity');

                if ($item['return_qty'] > $available) {
                    abort(422, 'Return quantity exceeds available stock');
                }

                WarehouseStockReturnItem::create([
                    'stock_return_id' => $return->id,
                    'batch_no' => $item['batch_id'],
                    'product_id' => $item['product_id'],
                    'return_qty' => $item['return_qty'],
                ]);
            }
        });

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
        DB::transaction(function () use ($id) {

            $return = WarehouseStockReturn::with('WarehouseStockReturnItem')->findOrFail($id);
            $user = auth()->user();

            if (
                $return->status !== 'approved' ||
                $user->warehouse_id !== $return->from_warehouse_id
            ) {
                abort(403);
            }

            foreach ($return->WarehouseStockReturnItem as $item) {

                StockMovement::create([
                    'warehouse_id' => $return->from_warehouse_id,
                    'product_batch_id' => $item->batch_no,
                    'quantity' => $item->return_qty,
                    'movement_type' => 'return',
                    'reference_type' => 'RETURN_TO_MASTER',
                    'reference_id' => $return->id,
                    'created_by' => $user->id,
                ]);
            }

            $return->update(['status' => 'dispatched']);
        });

        return back()->with('success', 'Stock dispatched');
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

                // ✅ If no damage handling UI yet, assume full quantity received
                $receivedQty = $item->return_qty;
                $damagedQty  = 0;

                // 1️⃣ UPDATE RETURN ITEM
                $item->update([
                    'received_qty' => $receivedQty,
                    'damaged_qty'  => $damagedQty,
                ]);
                StockMovement::create([
                    'warehouse_id' => $return->to_warehouse_id,
                    'product_batch_id' => $item->batch_no,
                    'quantity' => $item->return_qty,
                    'movement_type' => 'IN',
                    'reference_type' => 'RETURN_TO_MASTER',
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
