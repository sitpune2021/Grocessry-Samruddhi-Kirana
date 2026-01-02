<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WarehouseTransferItem;
use App\Models\Warehouse;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\WarehouseTransfer;
use App\Models\StockMovement;
use App\Models\WarehouseTransferRequest;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;


class WarehouseTransferRequestController extends Controller
{
    public function index()
{
    $requests = WarehouseTransferRequest::where(
        'from_warehouse_id',
        Auth::user()->warehouse_id
    )->with('items.product')->latest()->get();

    return view('warehouse_transfer.index', compact('requests'));
}

 

// public function create()
// {
//     $warehouses = Warehouse::where('id', '!=', auth()->user()->warehouse_id)->get();
//     $products   = Product::select('id','name')->get();

//     return view('warehouse_transfer.create', compact('warehouses','products'));
// }


public function create()
{
    $warehouses = Warehouse::where('id','!=',auth()->user()->warehouse_id)->get();

    $purchaseOrders = PurchaseOrder::where(
        'warehouse_id',
        auth()->user()->warehouse_id
    )->latest()->get();

    Log::info('USER WAREHOUSE', [
    'user_warehouse_id' => auth()->user()->warehouse_id
]);

    return view('warehouse_transfer.create',
        compact('warehouses','purchaseOrders')
    );
}

public function store(Request $request)
{
    Log::info('STORE HIT', $request->all());

    if (!$request->items) {
        return back()->withErrors(['items' => 'No items found']);
    }

    $items = json_decode($request->items, true);

    if (empty($items)) {
        return back()->withErrors(['items' => 'Empty items']);
    }

    DB::transaction(function () use ($request, $items) {

        $req = WarehouseTransferRequest::create([
            'from_warehouse_id' => auth()->user()->warehouse_id,
            'to_warehouse_id'   => $request->to_warehouse_id,
            'request_no'        => 'WR-' . time(),
            'request_date'      => now(),
            'status'            => 'pending',
        ]);

        foreach ($items as $item) {
            $req->items()->create([
                'product_id'    => $item['product_id'],
                'requested_qty' => $item['qty'],
            ]);
        }
    });

    return redirect()->route('warehouse_transfer.create')
        ->with('success', 'Request sent successfully');
}


public function items($id)
{
    $items = PurchaseOrderItem::with('product')
        ->where('purchase_order_id', $id)
        ->get();

    Log::info('PO ITEMS', $items->toArray());

    return response()->json($items);
}



public function incoming()
{
    $requests = WarehouseTransferRequest::where(
        'to_warehouse_id',
        Auth::user()->warehouse_id
    )->where('status', 'pending')
     ->with('items.product')
     ->get();

    return view('warehouse_transfer.incoming', compact('requests'));
}

public function approve($id)
{
    DB::transaction(function () use ($id) {

        $req = WarehouseTransferRequest::with('items')->findOrFail($id);

        foreach ($req->items as $item) {

            // ❌ REMOVE from FROM warehouse
            ProductBatch::where('warehouse_id', $req->from_warehouse_id)
                ->where('product_id', $item->product_id)
                ->decrement('quantity', $item->requested_qty);

            // ✅ ADD to TO warehouse
            ProductBatch::updateOrCreate(
                [
                    'warehouse_id' => $req->to_warehouse_id,
                    'product_id'   => $item->product_id,
                ],
                [
                    'quantity' => DB::raw('quantity + ' . $item->requested_qty)
                ]
            );

            $item->update([
                'approved_qty' => $item->requested_qty
            ]);
        }

        $req->update(['status' => 'approved']);
    });

    return back()->with('success', 'Request approved & stock transferred');
}

public function reject($id)
{
    WarehouseTransferRequest::where('id', $id)
        ->update(['status' => 'rejected']);

    return back()->with('success', 'Request rejected');
}


}
