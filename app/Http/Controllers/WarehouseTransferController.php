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
            //'categories' => Category::all(),
            'categories' => collect(), // initially empty
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

    // Multiple product store function
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->items as $item) {

                // server-side stock check
                $stock = WarehouseStock::where([
                    'warehouse_id' => $item['from_warehouse_id'],
                    'batch_id'     => $item['batch_id'],
                ])->first();

                if (!$stock || $stock->quantity < $item['quantity']) {
                    throw new \Exception('Insufficient stock');
                }

                // reduce from warehouse
                WarehouseStock::where([
                    'warehouse_id' => $item['from_warehouse_id'],
                    'batch_id'     => $item['batch_id'],
                ])->decrement('quantity', $item['quantity']);

                // reduce batch
                ProductBatch::where('id', $item['batch_id'])
                    ->decrement('quantity', $item['quantity']);

                // add to warehouse
                WarehouseStock::updateOrCreate(
                    [
                        'warehouse_id' => $item['to_warehouse_id'],
                        'batch_id'     => $item['batch_id'],
                    ],
                    [
                        'category_id' => $item['category_id'],
                        'product_id'  => $item['product_id'],
                        'quantity'    => DB::raw('quantity + '.$item['quantity']),
                    ]
                );

                // transfer record
                WarehouseTransfer::create($item);

                // stock movement
                StockMovement::create([
                    'type'             => 'transfer',
                    'quantity'         => $item['quantity'],
                    'product_batch_id' => $item['batch_id'],
                ]);
            }
        });

        return redirect()->route('transfer.index')
            ->with('success', 'Multiple products transferred successfully');
    }

    // Single product store function
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'from_warehouse_id' => 'required|different:to_warehouse_id|exists:warehouses,id',
    //         'to_warehouse_id'   => 'required|exists:warehouses,id',
    //         'category_id'       => 'required|exists:categories,id',
    //         'product_id'        => 'required|exists:products,id',
    //         'batch_id'          => 'required|exists:product_batches,id',
    //         'quantity'          => 'required|integer|min:1',
    //     ]);

    //     // Check available stock on server side
    //     $fromStock = WarehouseStock::where([
    //         'warehouse_id' => $data['from_warehouse_id'],
    //         'batch_id'     => $data['batch_id'],
    //     ])->first();

    //     if (!$fromStock || $fromStock->quantity < $data['quantity']) {
    //         return back()->withInput()->withErrors([
    //             'quantity' => "Cannot transfer more than available stock (" . ($fromStock ? $fromStock->quantity : 0) . ")"
    //         ]);
    //     }

    //     $batch = ProductBatch::findOrFail($data['batch_id']);

    //         if ($batch->is_blocked || $batch->expiry_date < now()->toDateString()) {
    //             return back()->withInput()->withErrors([
    //                 'batch_id' => "Cannot transfer expired or blocked batch ({$batch->batch_no})"
    //             ]);
    //         }

    //     DB::transaction(function () use ($data) {

    //         // FROM warehouse reduce
    //         WarehouseStock::where([
    //             'warehouse_id' => $data['from_warehouse_id'],
    //             'batch_id'     => $data['batch_id'],
    //         ])->decrement('quantity', $data['quantity']);

    //         // Reduce batch master quantity
    //         ProductBatch::where('id', $data['batch_id'])
    //             ->decrement('quantity', $data['quantity']);


    //         // TO warehouse add
    //         WarehouseStock::updateOrCreate(
    //             [
    //                 'warehouse_id' => $data['to_warehouse_id'],
    //                 'batch_id'     => $data['batch_id'],
    //             ],
    //             [
    //                 'category_id' => $data['category_id'],
    //                 'product_id'  => $data['product_id'],
    //                 'quantity'    => DB::raw('quantity + '.$data['quantity']),
    //             ]
    //         );


    //         // Warehouse transfer record
    //         $transfer = WarehouseTransfer::create($data);

    //         // Stock movement record
    //         StockMovement::create([
    //             'type'             => 'transfer',
    //             'quantity'         => $data['quantity'],
    //             'product_batch_id' => $data['batch_id'],
    //         ]);

    //         // -----------------------------
    //         // Logging the transfer
    //         // -----------------------------
    //         Log::info('Warehouse transfer created', [
    //             'transfer_id'       => $transfer->id,
    //             'from_warehouse_id' => $data['from_warehouse_id'],
    //             'to_warehouse_id'   => $data['to_warehouse_id'],
    //             'category_id'       => $data['category_id'],
    //             'product_id'        => $data['product_id'],
    //             'batch_id'          => $data['batch_id'],
    //             'quantity'          => $data['quantity'],
    //             'created_by'        => auth()->id(), // current logged-in user
    //             'timestamp'         => now(),
    //         ]);
    //     });

    //     return redirect()->route('transfer.index')
    //     ->with('success', 'Warehouse transfer completed');

    // }

   
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

        $categories = Category::whereIn('id', function ($q) use ($transfer) {
            $q->select('category_id')
            ->from('warehouse_stock')
            ->where('warehouse_id', $transfer->from_warehouse_id)
            ->where('quantity', '>', 0);
        })->get();

        $products = Product::where('category_id', $transfer->category_id)->get();
        $batches  = ProductBatch::where('product_id', $transfer->product_id)->get();

        return view('warehouse.transfer', compact(
            'transfer',
            'categories',
            'products',
            'batches'
        ) + [
            'warehouses' => Warehouse::where('status', 'active')->get(),
        ]);
    }

    // Update Method
    public function update(Request $request, $id)
    {
        $transfer = WarehouseTransfer::findOrFail($id);

        // 🔴 OLD VALUES
        $oldFromWarehouse = $transfer->from_warehouse_id;
        $oldToWarehouse   = $transfer->to_warehouse_id;
        $oldBatchId       = $transfer->batch_id;
        $oldQty           = $transfer->quantity;

        // ✅ Validation
        $validated = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'category_id'       => 'required|exists:categories,id',
            'product_id'        => 'required|exists:products,id',
            'batch_id'          => 'required|exists:product_batches,id',
            'quantity'          => 'required|integer|min:1',
        ]);

        DB::transaction(function () use (
            $transfer,
            $validated,
            $oldFromWarehouse,
            $oldToWarehouse,
            $oldBatchId,
            $oldQty
        ) {

            /* ---------------------------------
            | 1️⃣ ROLLBACK OLD TRANSFER
            ---------------------------------*/

            // OLD FROM → add back qty
            WarehouseStock::where([
                'warehouse_id' => $oldFromWarehouse,
                'batch_id'     => $oldBatchId,
            ])->increment('quantity', $oldQty);

            // OLD TO → reduce qty
            WarehouseStock::where([
                'warehouse_id' => $oldToWarehouse,
                'batch_id'     => $oldBatchId,
            ])->decrement('quantity', $oldQty);

            // Product batch qty restore
            ProductBatch::where('id', $oldBatchId)
                ->increment('quantity', $oldQty);

            /* ---------------------------------
            | 2️⃣ APPLY NEW TRANSFER
            ---------------------------------*/

            // NEW FROM → reduce qty
            WarehouseStock::where([
                'warehouse_id' => $validated['from_warehouse_id'],
                'batch_id'     => $validated['batch_id'],
            ])->decrement('quantity', $validated['quantity']);

            // NEW TO → add qty
            WarehouseStock::updateOrCreate(
                [
                    'warehouse_id' => $validated['to_warehouse_id'],
                    'batch_id'     => $validated['batch_id'],
                ],
                [
                    'category_id' => $validated['category_id'],
                    'product_id'  => $validated['product_id'],
                    'quantity'    => DB::raw('quantity + '.$validated['quantity']),
                ]
            );

            // Product batch qty reduce
            ProductBatch::where('id', $validated['batch_id'])
                ->decrement('quantity', $validated['quantity']);

            /* ---------------------------------
            | 3️⃣ UPDATE TRANSFER ROW
            ---------------------------------*/
            $transfer->update($validated);

            /* ---------------------------------
            | 4️⃣ LOG
            ---------------------------------*/
            Log::info('Warehouse transfer updated with stock sync', [
                'transfer_id' => $transfer->id,
                'old' => [
                    'from' => $oldFromWarehouse,
                    'to'   => $oldToWarehouse,
                    'batch'=> $oldBatchId,
                    'qty'  => $oldQty,
                ],
                'new' => $validated,
                'updated_by' => auth()->id(),
                'time' => now(),
            ]);
        });

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

    public function checkBatchValidity($batch_id)
    {
        $batch = ProductBatch::find($batch_id);

        if (!$batch) {
            return response()->json([
                'valid' => false,
                'message' => 'Batch not found'
            ]);
        }

        if ($batch->expiry_date < now()->toDateString()) {
            return response()->json([
                'valid' => false,
                'message' => "Batch {$batch->batch_no} is expired"
            ]);
        }

        if ($batch->is_blocked) {
            return response()->json([
                'valid' => false,
                'message' => "Batch {$batch->batch_no} is blocked"
            ]);
        }

        return response()->json([
            'valid' => true
        ]);
    }

    public function getCategoriesByWarehouse($warehouse_id)
    {
        $categoryIds = WarehouseStock::where('warehouse_id', $warehouse_id)
            ->where('quantity', '>', 0)
            ->pluck('category_id')
            ->unique();

        $categories = Category::whereIn('id', $categoryIds)
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }

    

}
