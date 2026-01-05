<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        $user = Auth::user();

        $returns = WarehouseStockReturn::with('items')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view(
            'menus.warehouse-stock-return.stock-return-index',
            compact('returns')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = Auth::user();

        $fromWarehouseId = $users->warehouse_id;

        $warehouses = Warehouse::where('id', '!=', $fromWarehouseId)
            ->get();
        $user = User::with('warehouse')->find(auth()->id());

        $warehouseStocks = WarehouseStock::with(['product', 'batch'])->where('warehouse_id', $fromWarehouseId)->get();

        return view('menus.warehouse-stock-return.stock-return', compact(
            'warehouses',
            'user',
            'warehouseStocks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            /** ✅ VALIDATION */
            $request->validate([
                'from_warehouse_id' => 'required|exists:warehouses,id',
                'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
                'return_reason'     => 'required|string',
                'remarks'           => 'nullable|string',

                'items'                     => 'required|array|min:1',
                'items.*.product_id'        => 'required|exists:products,id',
                'items.*.batch_id'          => 'required|exists:warehouse_stock,batch_id',
                'items.*.return_qty'        => 'required|integer|min:1',
                'items.*.product_image'     => 'nullable|image|max:2048',
            ]);

            /** 1️⃣ CREATE STOCK RETURN */
            $stockReturn = WarehouseStockReturn::create([
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'return_reason'     => $request->return_reason,
                'remarks'           => $request->remarks,
                'status'            => 'draft',
                'created_by'        => Auth::id(),
            ]);

            /** 2️⃣ LOOP ITEMS */
            foreach ($request->items as $item) {

                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $request->from_warehouse_id,
                    'product_id'   => $item['product_id'],
                    'batch_id'     => $item['batch_id'],
                ])->lockForUpdate()->first();

                if (!$warehouseStock) {
                    throw new \Exception('Stock not found for selected product & batch.');
                }

                if ($item['return_qty'] > $warehouseStock->quantity) {
                    throw new \Exception('Return quantity cannot exceed available stock.');
                }

                /** IMAGE UPLOAD */
                $imagePath = null;
                if (!empty($item['product_image'])) {
                    $imagePath = $item['product_image']->store('stock-returns', 'public');
                }

                /** 3️⃣ INSERT INTO warehouse_stock_returns_item */
                WarehouseStockReturnItem::create([
                    'stock_return_id' => $stockReturn->id,
                    'product_id'      => $item['product_id'],
                    'batch_id'        => $item['batch_id'],
                    'return_qty'      => $item['return_qty'],
                    'product_image'   => $imagePath,
                    'condition'       => 'good', // default
                ]);
            }

            DB::commit();

            return redirect()
                ->route('stock-returns.index')
                ->with('success', 'Warehouse stock return saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Warehouse stock return failed', [
                'user_id' => Auth::id(),
                'error'   => $e->getMessage()
            ]);

            return back()->withInput()->with('error', $e->getMessage());
        }
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function submit($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);

        if ($return->status !== 'draft') {
            return back()->with('error', 'Only draft can be submitted');
        }

        $return->update([
            'status' => 'submitted'
        ]);

        return back()->with('success', 'Stock return submitted for approval');
    }
    public function approve($id)
    {
        $return = WarehouseStockReturn::findOrFail($id);

        if ($return->status !== 'submitted') {
            return back()->with('error', 'Invalid status');
        }

        $return->update([
            'status'      => 'approved',
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Stock return approved');
    }
}
