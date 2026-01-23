<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SupplierChallan;
use App\Models\SupplierChallanItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class SupplierChallenController extends Controller
{


    public function index()
    {
        $challans = SupplierChallan::with([
            'purchaseOrder',
            'supplier',
            'warehouse'
        ])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('supplier_challan.index', compact('challans'));
    }

    private function generateChallanNo()
    {
        $lastId = SupplierChallan::max('id') + 1;
        return 'SCH-' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
    }

    public function create()
    {
        // if (auth()->user()->role_id != 2) {
        //     abort(403, 'Unauthorized');
        // }

        $warehouse = Warehouse::where('type', 'master')
            ->where('status', 1)
            ->first();

        if (!$warehouse) {
            abort(500, 'Master warehouse not configured');
        }

        return view('supplier_challan.create', [
            'mode' => 'add',
            'warehouse' => $warehouse,   // ✅ SINGLE warehouse
            'suppliers' => Supplier::all(),
            'categories' => Category::all(),
            'autoChallanNo' => $this->generateChallanNo(),
        ]);
    }

    public function store(Request $request)
    {
        // 🔹 START LOG
        Log::info('Supplier Challan store process started', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'payload' => $request->all(),
        ]);

        $request->validate([
            'warehouse_id' => 'required',
            'supplier_id' => 'required',
            'challan_no' => 'required',
            'challan_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.received_qty' => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();

        try {

            // 🔹 CREATE CHALLAN MASTER
            $challan = SupplierChallan::create([
                'warehouse_id' => $request->warehouse_id,
                'supplier_id' => $request->supplier_id,
                'challan_no' => $request->challan_no,
                'challan_date' => $request->challan_date,
                'status' => 'received',
                'created_by' => auth()->id(),
            ]);

            Log::info('Supplier Challan master created', [
                'challan_id' => $challan->id,
                'challan_no' => $challan->challan_no,
            ]);

            // 🔹 CREATE CHALLAN ITEMS
            foreach ($request->items as $index => $item) {

                SupplierChallanItem::create([
                    'supplier_challan_id' => $challan->id,
                    'category_id'        => $item['category_id'],
                    'sub_category_id'    => $item['sub_category_id'],
                    'product_id'         => $item['product_id'],
                    'ordered_qty'        => $item['received_qty'],
                    'received_qty'       => $item['received_qty'],
                ]);

                Log::info('Supplier Challan item added', [
                    'challan_id' => $challan->id,
                    'product_id' => $item['product_id'],
                    'received_qty' => $item['received_qty'],
                    'row_index' => $index,
                ]);
            }

            DB::commit();

            // 🔹 SUCCESS LOG
            Log::info('Supplier Challan store process completed successfully', [
                'challan_id' => $challan->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('supplier_challan.index')
                ->with('success', 'Supplier Challan added successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            // 🔴 ERROR LOG
            Log::error('Supplier Challan store process failed', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'payload' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong while saving challan');
        }
    }

    public function show(string $id)
    {
        $challan = SupplierChallan::with([
            'items.product.subCategory.category',
            'supplier',
            'warehouse'
        ])->findOrFail($id);

        return view('supplier_challan.create', [
            'mode' => 'view',
            'challan' => $challan,
            'warehouse' => $challan->warehouse,
            'suppliers' => Supplier::all(),
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
            'autoChallanNo' => $challan->challan_no,
        ]);
    }

    public function edit(string $id)
    {
        $challan = SupplierChallan::with([
            'items.product',
            'supplier',
            'warehouse',
            'purchaseOrder'
        ])
            ->findOrFail($id);

        return view('supplier_challan.create', [
            'mode' => 'edit',
            'challan' => $challan,
            'warehouse' => $challan->warehouse,
            'suppliers' => Supplier::all(),
            'categories' => Category::all(),
            'purchaseOrders' => PurchaseOrder::all()
        ]);
    }

    public function update(Request $request, string $id)
    {
        // 🔹 START LOG
        Log::info('Supplier Challan update process started', [
            'challan_id' => $id,
            'user_id' => auth()->id(),
            'payload' => $request->all(),
        ]);

        $request->validate([
            'challan_no' => 'required|string',
            'challan_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.received_qty' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {

            // 🔹 FETCH CHALLAN WITH ITEMS
            $challan = SupplierChallan::with('items')->findOrFail($id);

            Log::info('Supplier Challan fetched for update', [
                'challan_id' => $challan->id,
                'items_count' => $challan->items->count(),
            ]);

            // 1️⃣ UPDATE CHALLAN MASTER
            $challan->update([
                'challan_no' => $request->challan_no,
                'challan_date' => $request->challan_date,
                'status' => 'received',
            ]);

            Log::info('Supplier Challan master updated', [
                'challan_id' => $challan->id,
                'challan_no' => $challan->challan_no,
            ]);

            $isPartial = false;

            // 2️⃣ UPDATE ITEMS (NO STOCK UPDATE)
            foreach ($request->items as $index => $item) {

                if (!isset($challan->items[$index])) {
                    Log::warning('Supplier Challan item index missing', [
                        'challan_id' => $challan->id,
                        'index' => $index,
                    ]);
                    continue;
                }

                $challanItem = $challan->items[$index];

                $oldQty = $challanItem->received_qty;
                $newQty = $item['received_qty'];

                // 🔹 UPDATE ITEM ONLY
                $challanItem->update([
                    'received_qty' => $newQty,
                ]);

                Log::info('Supplier Challan item updated', [
                    'challan_id' => $challan->id,
                    'product_id' => $challanItem->product_id,
                    'old_qty' => $oldQty,
                    'new_qty' => $newQty,
                ]);

                // 🔹 CHECK PARTIAL STATUS
                if ($newQty < $challanItem->ordered_qty) {
                    $isPartial = true;
                }
            }

            // 3️⃣ UPDATE STATUS IF PARTIAL
            if ($isPartial) {
                $challan->update(['status' => 'partial']);

                Log::info('Supplier Challan marked as PARTIAL', [
                    'challan_id' => $challan->id,
                ]);
            }

            DB::commit();

            // ✅ SUCCESS LOG
            Log::info('Supplier Challan update completed successfully', [
                'challan_id' => $challan->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('supplier_challan.index')
                ->with('success', 'Supplier Challan updated successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            // 🔴 ERROR LOG
            Log::error('Supplier Challan update failed', [
                'challan_id' => $id,
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'payload' => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong while updating challan');
        }
    }

    public function destroy(string $id)
    {
        if (!hasPermission('supplier_challan.delete')) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $challan = SupplierChallan::findOrFail($id);

            // delete items first
            SupplierChallanItem::where('supplier_challan_id', $id)->delete();

            // delete challan
            $challan->delete();

            DB::commit();

            return redirect()
                ->route('supplier_challan.index')
                ->with('success', 'Supplier Challan deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


}
