<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\Category;


class WarehouseTransferController extends Controller
{
    

    public function index()
    {
        // Eager load related models for display
        $transfers = WarehouseTransfer::with([
            'fromWarehouse', 
            'toWarehouse', 
            'category', 
            'product', 
            'batch'
        ])->orderBy('created_at', 'desc')->get();

        return view('warehouse.index', compact('transfers'));
    }
 
    public function create()
    {
        return view('warehouse.transfer', [
            'warehouses' => Warehouse::where('status', 'active')->get(),
            'categories' => Category::all(),
            'transfer'   => null, // important
        ]);
    }
    
    public function getProductsByCategory($category_id)
    {
        return Product::where('category_id', $category_id)->get();
    }

    public function getBatchesByProduct($product_id)
    {
        return ProductBatch::where('product_id', $product_id)->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_warehouse_id' => 'required|different:to_warehouse_id|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id',
            'category_id'       => 'required|exists:categories,id',
            'product_id'        => 'required|exists:products,id',
            'batch_id'          => 'required|exists:product_batches,id',
            'quantity'          => 'required|integer|min:1',
        ]);

        // Check available stock on server side
        $fromStock = WarehouseStock::where([
            'warehouse_id' => $data['from_warehouse_id'],
            'batch_id'     => $data['batch_id'],
        ])->first();

        if (!$fromStock || $fromStock->quantity < $data['quantity']) {
            return back()->withInput()->withErrors([
                'quantity' => "Cannot transfer more than available stock (" . ($fromStock ? $fromStock->quantity : 0) . ")"
            ]);
        }

        DB::transaction(function () use ($data) {

            // FROM warehouse reduce
            WarehouseStock::where([
                'warehouse_id' => $data['from_warehouse_id'],
                'batch_id'     => $data['batch_id'],
            ])->decrement('quantity', $data['quantity']);

            // TO warehouse add
            WarehouseStock::updateOrCreate(
                [
                    'warehouse_id' => $data['to_warehouse_id'],
                    'batch_id'     => $data['batch_id'],
                ],
                [
                    'category_id' => $data['category_id'],
                    'product_id'  => $data['product_id'],
                    'quantity'    => DB::raw('quantity + '.$data['quantity']),
                ]
            );

            $batch = ProductBatch::findOrFail($data['batch_id']);

            if ($batch->is_blocked || $batch->expiry_date < now()->toDateString()) {
                return back()->withInput()->withErrors([
                    'batch_id' => "Cannot transfer expired or blocked batch ({$batch->batch_no})"
                ]);
            }


            // Warehouse transfer record
            $transfer = WarehouseTransfer::create($data);

            // Stock movement record
            StockMovement::create([
                'type'             => 'transfer',
                'quantity'         => $data['quantity'],
                'product_batch_id' => $data['batch_id'],
            ]);

            // -----------------------------
            // Logging the transfer
            // -----------------------------
            Log::info('Warehouse transfer created', [
                'transfer_id'       => $transfer->id,
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id'   => $data['to_warehouse_id'],
                'category_id'       => $data['category_id'],
                'product_id'        => $data['product_id'],
                'batch_id'          => $data['batch_id'],
                'quantity'          => $data['quantity'],
                'created_by'        => auth()->id(), // current logged-in user
                'timestamp'         => now(),
            ]);
        });

        return redirect()->back()->with('success', 'Warehouse transfer completed');
    }

    public function getWarehouseStock($warehouse_id, $batch_id)
    {
        $stock = WarehouseStock::where([
            'warehouse_id' => $warehouse_id,
            'batch_id'     => $batch_id,
        ])->first();

        return response()->json([
            'quantity' => $stock ? $stock->quantity : 0
        ]);
    }

    // Edit Method 
    public function edit($id)
    {
        $transfer = WarehouseTransfer::with(['product', 'batch'])->findOrFail($id);

        $products = Product::where('category_id', $transfer->category_id)->get();
        $batches  = ProductBatch::where('product_id', $transfer->product_id)->get();

        return view('warehouse.transfer', [
            'warehouses' => Warehouse::where('status', 'active')->get(),
            'categories' => Category::all(),
            'products'   => $products,
            'batches'    => $batches,
            'transfer'   => $transfer,
        ]);
    }

    // Update Method
    public function update(Request $request, $id)
    {
        $transfer = WarehouseTransfer::findOrFail($id);

        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'category_id'       => 'required|exists:categories,id',
            'product_id'        => 'required|exists:products,id',
            'batch_id'          => 'required|exists:product_batches,id',
            'quantity'          => 'required|integer|min:1',
        ]);

        $transfer->update($validated);

        // -----------------------------
        // Logging the update action
        // -----------------------------
        Log::info('Warehouse transfer updated', [
            'transfer_id'       => $transfer->id,
            'from_warehouse_id' => $validated['from_warehouse_id'],
            'to_warehouse_id'   => $validated['to_warehouse_id'],
            'category_id'       => $validated['category_id'],
            'product_id'        => $validated['product_id'],
            'batch_id'          => $validated['batch_id'],
            'quantity'          => $validated['quantity'],
            'updated_by'        => auth()->id(), // current logged-in user
            'timestamp'         => now(),
        ]);

        return redirect()
            ->route('transfer.index')
            ->with('success', 'Warehouse transfer updated successfully');
    }

    public function destroy($id)
    {
        $batch = ProductBatch::findOrFail($id);
        $batch->delete(); // soft delete
        return redirect()->route('warehouse.index')->with('success', 'Batch deleted successfully');
    }

    public function show($id)
    {
        $transfer = WarehouseTransfer::with([
            'product',
            'batch',
            'fromWarehouse',
            'toWarehouse'
        ])->findOrFail($id);

        return view('warehouse.show', compact('transfer'));
    }


}
