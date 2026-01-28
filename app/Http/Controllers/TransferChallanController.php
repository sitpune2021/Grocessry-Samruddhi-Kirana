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

        $challans = $query->latest()->paginate(20);
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
        // 🔍 LOG REQUEST DATA
        Log::info('TransferChallan Store Request', $request->all());

        // Validate inputs
        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id'   => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'     => 'required|date',
            'products'          => 'required|array|min:1',
            'products.*'        => 'required|exists:products,id',
            'quantities'        => 'required|array|min:1',
            'quantities.*'      => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 🔍 LOG CHALLAN CREATE DATA
            Log::info('Creating TransferChallan', [
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'transfer_date'     => $request->transfer_date,
                'created_by'        => auth()->id(),
            ]);

            $challan = TransferChallan::create([
                'challan_no'        => $this->generateChallanNumber(),
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id'   => $request->to_warehouse_id,
                'transfer_date'     => $request->transfer_date,
                'status'            => 'pending',
                'created_by'        => auth()->id(),
            ]);

            Log::info('TransferChallan Created', $challan->toArray());

            foreach ($request->products as $index => $productId) {

                // 🔍 LOG PRODUCT LOOP
                Log::info('Processing Product', [
                    'index'     => $index,
                    'product_id' => $productId,
                    'quantity'  => $request->quantities[$index],
                ]);

                // 🔹 Fetch warehouse transfer
                $transfer = WarehouseTransfer::where('approved_by_warehouse_id', $request->from_warehouse_id)
                    ->where('requested_by_warehouse_id', $request->to_warehouse_id)
                    ->where('product_id', $productId)
                    ->where('status', 0)
                    ->first();

                // 🔍 LOG TRANSFER RESULT
                Log::info('WarehouseTransfer Result', [
                    'exists'    => (bool) $transfer,
                    'transfer_id' => $transfer?->id,
                    'batch_no'  => $transfer?->batch_no,
                ]);

                // 🔍 LOG CHALLAN ITEM DATA
                Log::info('Creating TransferChallanItem', [
                    'transfer_challan_id' => $challan->id,
                    'product_id'          => $productId,
                    'batch_no'            => $transfer?->batch_no,
                    'quantity'            => $request->quantities[$index],
                ]);

                // 🚨 HARD FAIL IF BATCH MISSING (TEMP DEBUG)
                if (!$transfer || !$transfer->batch_no) {
                    Log::error('Batch ID missing for product', [
                        'product_id' => $productId,
                        'transfer'   => $transfer?->toArray(),
                    ]);
                }

                TransferChallanItem::create([
                    'transfer_challan_id' => $challan->id,
                    'product_id'          => $productId,
                    'batch_no'            => $transfer?->batch_id, // ✅ ONLY CHANGE
                    'quantity'            => $request->quantities[$index],
                ]);


                // 🔍 LOG LAST INSERT
                $lastItem = TransferChallanItem::latest('id')->first();
                Log::info('Last Inserted TransferChallanItem', $lastItem?->toArray());

                // 🔹 Update warehouse transfer
                WarehouseTransfer::where('id', $transfer->id ?? null)->update([
                    'challan_id' => $challan->id,
                    'status'     => 0,
                ]);
            }

            DB::commit();

            Log::info('TransferChallan transaction committed', [
                'challan_id' => $challan->id,
            ]);

            return redirect()
                ->route('transfer-challans.index')
                ->with('success', 'Transfer Challan created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('TransferChallan store failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return back()->withInput()->with(
                'error',
                'Something went wrong. Please check logs.'
            );
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

            $transfer = WarehouseTransfer::where('approved_by_warehouse_id', $request->from_warehouse_id)
                ->where('requested_by_warehouse_id', $request->to_warehouse_id)
                ->where('product_id', $productId)
                ->where('status', 0)
                ->first();

            TransferChallanItem::create([
                'transfer_challan_id' => $transferChallan->id,
                'product_id'          => $productId,
                'batch_no'            => $transfer?->batch_id, // ✅ SAME FIX
                'quantity'            => $request->quantities[$i],
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
