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
use App\Models\SubCategory;

class SupplierChallenController extends Controller
{


    public function index()
    {
        $challans = SupplierChallan::with([
            // 'purchaseOrder',
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
        $user = auth()->user();

        // SUPER ADMIN (role_id = 1)
        if ($user->role_id == 1) {

            $warehouses = Warehouse::where('type', 'master')
                ->where('status', 1)
                ->get();

            $selectedWarehouse = null;

        } else {

            // MASTER USER →  warehouse
            $selectedWarehouse = Warehouse::where('id', $user->warehouse_id)
                ->where('status', 1)
                ->first();

            $warehouses = collect([$selectedWarehouse]); // single array convert
        }

        return view('supplier_challan.create', [
            'mode' => 'add',
            'warehouse' => $selectedWarehouse,
            'warehouses' => $warehouses,
            'suppliers' => Supplier::all(),
            'categories' => Category::all(),
            'products' => Product::all(),
            'autoChallanNo' => $this->generateChallanNo(),
        ]);
    }

    public function store(Request $request)
    {
        $startTime = microtime(true);

        // 🔹 START LOG
        Log::info('SupplierChallan | Store started', [
            'user_id' => auth()->id(),
            'ip'      => $request->ip(),
            'route'   => request()->route()->getName(),
        ]);

        // 🔹 VALIDATION
            $validated = $request->validate([
                'warehouse_id' => 'required',
                'supplier_id'  => 'required',
                'challan_no'   => 'required',
                'challan_date' => 'required|date',

                'items' => 'required|array|min:1',
                'items.*.category_id'     => 'required',
                'items.*.sub_category_id' => 'required',
                'items.*.product_id'      => 'required',
                'items.*.received_qty'    => 'required|numeric|min:1',
            ]);

            Log::info('SupplierChallan | Validation passed', [
                'items_count' => count($validated['items']),
            ]);

        try {

            DB::beginTransaction();

            // 🔹 CREATE CHALLAN MASTER
            $challan = SupplierChallan::create([
                'warehouse_id' => $validated['warehouse_id'],
                'supplier_id'  => $validated['supplier_id'],
                'challan_no'   => $validated['challan_no'],
                'challan_date' => $validated['challan_date'],
                'status'       => 'received',
                'created_by'   => auth()->id(),
            ]);

            Log::info('SupplierChallan | Master created', [
                'challan_id' => $challan->id,
                'challan_no' => $challan->challan_no,
            ]);

            // 🔹 CREATE CHALLAN ITEMS
            foreach ($validated['items'] as $index => $item) {

                SupplierChallanItem::create([
                    'supplier_challan_id' => $challan->id,
                    'category_id'         => $item['category_id'],
                    'sub_category_id'     => $item['sub_category_id'],
                    'product_id'          => $item['product_id'],
                    'ordered_qty'         => $item['received_qty'],
                    'received_qty'        => $item['received_qty'],
                    'rate'                => $item['rate'] ?? 0,
                ]);

                Log::info('SupplierChallan | Item created', [
                    'challan_id'   => $challan->id,
                    'product_id'   => $item['product_id'],
                    'received_qty' => $item['received_qty'],
                    'row_index'    => $index,
                ]);
            }

            DB::commit();

            // 🔹 SUCCESS LOG
            Log::info('SupplierChallan | Store completed', [
                'challan_id' => $challan->id,
                'duration_ms' => round((microtime(true) - $startTime) * 1000),
            ]);

            return redirect()
                ->route('supplier_challan.index')
                ->with('success', 'Supplier Challan added successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            // 🔴 ERROR LOG (FULL TRACE)
            Log::error('SupplierChallan | Store failed', [
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Something went wrong while saving challan');
        }
    }

    public function show(string $id)
    {
        $challan = SupplierChallan::with([
            'items.category',
            'items.subCategory',
            'items.product',
            'supplier',
            'warehouse'
        ])->findOrFail($id);

        $user = auth()->user();

        if ($user->role_id == 1) {
            $warehouses = Warehouse::where('type', 'master')
                ->where('status', 1)
                ->get();
        } else {
            $warehouses = collect([$challan->warehouse]);
        }

        return view('supplier_challan.create', [
            'mode'          => 'view',
            'challan'       => $challan,
            'warehouse'     => $challan->warehouse,
            'suppliers'     => Supplier::all(),
            'categories'    => Category::all(),
            'warehouses'    => $warehouses,
            'autoChallanNo' => $challan->challan_no,
        ]);
    }

    public function edit(string $id)
    {
        $challan = SupplierChallan::with([
            'items.category',      
            'items.subCategory',  
            'items.product',   
            'supplier',
            'warehouse',
        ])->findOrFail($id);

        $user = auth()->user();

        if ($user->role_id == 1) {
            $warehouses = Warehouse::where('type', 'master')
                ->where('status', 1)
                ->get();
        } else {
            $warehouses = collect([$challan->warehouse]);
        }

        return view('supplier_challan.create', [
            'mode' => 'edit',
            'challan' => $challan,
            'warehouse' => $challan->warehouse,
            'suppliers' => Supplier::all(),
            'categories' => Category::all(),
            'products' => Product::all(), 
            'warehouses'    => $warehouses,
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

    public function subcategories(Request $request)
    {
        $categoryIds = is_array($request->category_ids)
            ? $request->category_ids
            : explode(',', $request->category_ids);

        return response()->json([
            'data' => SubCategory::whereIn('category_id', $categoryIds)->get()
        ]);
    }

    public function productsBySubCategory(Request $request)
    {
        $products = Product::with(['subCategory.category'])
            ->whereIn('sub_category_id', $request->sub_category_ids)
            ->get();

        return response()->json([
            'data' => $products
        ]);
    }


}
