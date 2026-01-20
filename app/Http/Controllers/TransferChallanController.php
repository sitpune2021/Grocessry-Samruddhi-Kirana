<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransferChallan;
use App\Models\Warehouse;
use App\Models\TransferChallanItem;
use App\Models\Product;
use App\Models\WarehouseTransfer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\PDF;

class TransferChallanController extends Controller
{
    
    public function index(Request $request)
    {
        $query = TransferChallan::with(['fromWarehouse', 'toWarehouse']);

        if ($request->filled('warehouse_id')) { // only apply if not empty
            $query->where('from_warehouse_id', $request->warehouse_id)
                ->orWhere('to_warehouse_id', $request->warehouse_id);
        }

        $challans = $query->latest()->paginate(10);
        $warehouses = Warehouse::all();

        return view('Transfer_Challan.index', compact('challans', 'warehouses'));
    }

    public function create(Request $request)
    {
        $warehouses = Warehouse::all();
        $products = Product::all();

        $transferItems = [];

        if ($request->filled('transfer_group')) {
            [$from, $to] = explode('_', $request->transfer_group);

            $transferItems = WarehouseTransfer::with('product')
                ->where('approved_by_warehouse_id', $from)
                ->where('requested_by_warehouse_id', $to)
                ->where('status', 0)
                ->get();
        }

        return view('Transfer_Challan.create', [
            'mode' => 'add',
            'warehouses' => $warehouses,
            'products' => $products,
            'transferItems' => $transferItems,
            'fromWarehouse' => $request->from_warehouse_id,
            'toWarehouse' => $request->to_warehouse_id,
        ]);
    }

    private function generateChallanNumber()
    {
        $last = TransferChallan::latest('id')->first();

        $nextId = $last ? $last->id + 1 : 1;

        return 'TC-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
        // Validate inputs
        $request->validate([
            //'challan_no' => 'required|unique:transfer_challans,challan_no',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
        ], [
            // 'challan_no.required' => 'Challan number is required.',
            // 'challan_no.unique' => 'This Challan number is already taken.',
            'from_warehouse_id.required' => 'Please select a From Warehouse.',
            'to_warehouse_id.required' => 'Please select a To Warehouse.',
            'to_warehouse_id.different' => 'From and To Warehouse cannot be the same.',
            'transfer_date.required' => 'Transfer date is required.',
            'products.required' => 'Please select at least one product.',
            'quantities.required' => 'Please enter quantity for each product.',
        ]);

        DB::beginTransaction();

        try {
            $challan = TransferChallan::create([
                //'challan_no' => $request->challan_no,
                'challan_no' => $this->generateChallanNumber(),
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_date' => $request->transfer_date,
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            foreach ($request->products as $index => $productId) {
                
                // 1. Save challan item
                TransferChallanItem::create([
                    'transfer_challan_id' => $challan->id,
                    'product_id' => $productId,
                    'quantity' => $request->quantities[$index],
                ]);

                // 2. Link challan_id to warehouse_transfers (ONLY request rows)
                WarehouseTransfer::where('approved_by_warehouse_id', $request->from_warehouse_id)
                    ->where('requested_by_warehouse_id', $request->to_warehouse_id)
                    ->where('product_id', $productId)
                    ->where('status', 0)   // only requested stock
                    ->update([
                        'challan_id' => $challan->id,
                        'status' => 0       // dispatched
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('transfer-challans.index')
                ->with('success', 'Transfer Challan created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $transferChallan = TransferChallan::with('items')->findOrFail($id);
        $warehouses = Warehouse::all();
        $products = Product::all();

        return view('Transfer_Challan.create', [
            'mode' => 'edit',
            'transferChallan' => $transferChallan,
            'transferChallanItems' => $transferChallan->items,
            'warehouses' => $warehouses,
            'products' => $products,
        ]);
    }

    public function update(Request $request, $id)
    {
        $transferChallan = TransferChallan::findOrFail($id);

        $request->validate([
            'challan_no' => 'required|unique:transfer_challans,challan_no,' . $id,
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
        ], [
            'challan_no.required' => 'Challan number is required.',
            'challan_no.unique' => 'This Challan number is already taken.',
            'from_warehouse_id.required' => 'Please select a From Warehouse.',
            'to_warehouse_id.required' => 'Please select a To Warehouse.',
            'to_warehouse_id.different' => 'From and To Warehouse cannot be the same.',
            'transfer_date.required' => 'Transfer date is required.',
            'products.required' => 'Please select at least one product.',
            'quantities.required' => 'Please enter quantity for each product.',
        ]);

        $transferChallan->update([
            'challan_no' => $request->challan_no,
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id' => $request->to_warehouse_id,
            'transfer_date' => $request->transfer_date,
        ]);

        $transferChallan->items()->delete();

        foreach ($request->products as $i => $productId) {
            TransferChallanItem::create([
                'transfer_challan_id' => $transferChallan->id,
                'product_id' => $productId,
                'quantity' => $request->quantities[$i],
            ]);
        }

        return redirect()
            ->route('transfer-challans.index')
            ->with('success', 'Transfer Challan updated successfully');
    }

    public function show(TransferChallan $transferChallan)
    {
        $transferChallan->load(['items.product', 'fromWarehouse', 'toWarehouse']);

        return view('Transfer_Challan.create', [
            'mode' => 'view',
            'transferChallan' => $transferChallan,
            'transferChallanItems' => $transferChallan->items,
            'warehouses' => Warehouse::all(),
            'products' => Product::all(),
        ]);
    }

    public function destroy(TransferChallan $transferChallan)
    {
        $transferChallan->items()->delete();
        $transferChallan->delete();

        return redirect()->route('transfer-challans.index')->with('success', 'Transfer Challan deleted successfully.');
    }

    public function downloadPdf(TransferChallan $transferChallan)
    {
        $transferChallan->load(['items.product', 'fromWarehouse', 'toWarehouse']);

        return view('Transfer_Challan.challen_pdf', [
            'challan' => $transferChallan
        ]);
    }

    public function downloadCsv(TransferChallan $transferChallan)
    {
        $transferChallan->load(['items.product', 'fromWarehouse', 'toWarehouse']);

        $filename = 'Transfer_Challan_' . $transferChallan->challan_no . '.csv';

        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function () use ($transferChallan) {
            $file = fopen('php://output', 'w');

            // Header Info
            fputcsv($file, ['Challan No', $transferChallan->challan_no]);
            fputcsv($file, ['Transfer Date', $transferChallan->transfer_date]);
            fputcsv($file, ['From Warehouse', $transferChallan->fromWarehouse->name]);
            fputcsv($file, ['To Warehouse', $transferChallan->toWarehouse->name]);
            fputcsv($file, []);

            // Table Header
            fputcsv($file, ['Sr No', 'Product', 'Quantity']);

            foreach ($transferChallan->items as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->product->name,
                    $item->quantity
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

   public function deleteTransfer($id)
{
    $transfer = WarehouseTransfer::find($id);

    if (!$transfer) {
        return response()->json([
            'success' => false,
            'message' => 'Record not found'
        ], 404);
    }

    // Sirf pending (status = 0) allow
    if ($transfer->status != 0) {
        return response()->json([
            'success' => false,
            'message' => 'Only pending requests can be removed'
        ], 400);
    }

    $transfer->delete();   // ✅ Only this table

    return response()->json([
        'success' => true,
        'message' => 'Product removed successfully'
    ]);
}

    

}
