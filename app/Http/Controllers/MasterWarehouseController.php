<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\MasterWarehouse;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\District;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::paginate(10);
        return view('menus.warehouse.master.index', compact('warehouses'));
    }

    public function create()
    {
        $mode = 'add';
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $countries = Country::all();
        $districts = District::orderBy('name')->get();


        return view('menus.warehouse.master.add-warehouse', compact('mode', 'warehouses', 'categories', 'countries', 'districts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:master,district,taluka',
            'contact_number' => 'required|digits:10',

            'parent_id'   => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'district_id' => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'taluka_id'   => 'nullable|required_if:type,taluka|integer',
        ]);

        $data = [
            'name'           => $request->name,
            'type'           => $request->type,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'email'          => $request->email,
            'country_id'     => $request->country_id,
            'state_id'       => $request->state_id,
            'status'         => 'active',
        ];

        if ($request->type === 'master') {
            $data['parent_id']   = null;
            $data['district_id'] = null;
            $data['taluka_id']   = null;
        }

        if ($request->type === 'district') {
            $data['parent_id']   = $request->parent_id;
            $data['district_id'] = $request->district_id;
            $data['taluka_id']   = null;
        }

        if ($request->type === 'taluka') {
            $data['parent_id']   = $request->parent_id;
            $data['district_id'] = $request->district_id;
            $data['taluka_id']   = $request->taluka_id;
        }

        Warehouse::create($data);

        return redirect()->route('warehouse.index')
            ->with('success', 'Warehouse created successfully');
    }

    // public function store(Request $request)
    // {
    //     try {
    //         $rules = [
    //             'name'           => 'required|string|max:255',
    //             'type'           => 'required|in:master,district,taluka',
    //             'address'        => 'nullable|string|max:500',
    //             'contact_person' => 'nullable|string|max:255',
    //             'contact_number'         => 'nullable|string|max:15',
    //             'email'          => 'nullable|email',


    //             'parent_id'  => 'required_if:type,district,required_if:type,taluka|nullable|integer',
    //             'district_id' => 'required_if:type,district,required_if:type,taluka|nullable|integer',
    //             'taluka_id'  => 'required_if:type,taluka|nullable|integer',
    //         ];
    //         $messages = [
    //             'parent_id.required_if'   => 'Parent warehouse is required.',
    //             'district_id.required_if' => 'District is required.',
    //             'taluka_id.required_if'   => 'Taluka is required.',
    //         ];

    //         $validated = $request->validate($rules, $messages);

    //         Log::info('Warehouse Store Request:', $request->all());

    //         $data = [
    //             'name'            => $request->name,
    //             'type'            => $request->type,
    //             'address'         => $request->address,
    //             'contact_person'  => $request->contact_person,
    //             'contact_number' => $request->contact_number,
    //             'email'          => $request->email,
    //             'country_id'          => $request->country_id,
    //             'state_id'          => $request->state_id,
    //             'status'          => 'active',
    //         ];

    //         if ($request->type == 'master') {
    //             $data['parent_id']  = null;
    //             $data['district_id'] = $request->district_id;;
    //             $data['taluka_id']   = null;
    //         }

    //         if ($request->type == 'district') {
    //             $data['parent_id']   = $request->parent_id;   // master
    //             $data['district_id'] = $request->district_id;
    //             $data['taluka_id']   = null;
    //         }

    //         if ($request->type == 'taluka') {
    //             $data['parent_id']   = $request->parent_id;
    //             $data['district_id'] = $request->district_id;
    //             $data['taluka_id']   = $request->taluka_id;
    //         }

    //         $warehouse = Warehouse::create($data);

    //         Log::info('Warehouse Created Successfully:', $warehouse->toArray());

    //         // if ($request->filled('email')) {

    //         //     $defaultPassword = 'Warehouse@123';

    //         //     User::create([
    //         //         'name'         => $request->name,
    //         //         'email'        => $request->email,
    //         //         'mobile'       => $request->mobile,
    //         //         'password'     => Hash::make($defaultPassword),
    //         //         'role_id'      => 2, // warehouse user role
    //         //         'warehouse_id' => $warehouse->id,
    //         //         'status'       => 'active',
    //         //     ]);
    //         // }

    //         return redirect()->route('warehouse.index')->with('success', 'Warehouse created successfully.');
    //     } catch (\Exception $e) {

    //         Log::error('Warehouse Store Error:', [
    //             'error_message' => $e->getMessage(),
    //             'line'          => $e->getLine(),
    //             'file'          => $e->getFile(),
    //         ]);

    //         return back()->with('error', 'Something went wrong, please try again.');
    //     }
    // }

    public function show($id)
    {
        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])->findOrFail($id);
            $countries = Country::all();
            $districts = District::all();   // 🔹 this was missing

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'view', // view mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
                'districts' => $districts,

            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse Show Error', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }

    public function edit($id)
    {
        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])->findOrFail($id);
            $countries = Country::all();
            $districts = District::all(); // 🔹 important

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'edit', // edit mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
                'districts' => $districts,

            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse Edit Error', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:master,district,taluka',
                'address' => 'nullable|string|max:500',
                'contact_person' => 'nullable|string|max:255',
                'contact_number' => 'nullable|string|max:15',
                'email' => 'nullable|email',
            ]);

            $warehouse->update([
                'name' => $request->name,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'district_id' => $request->district_id,
                'taluka_id' => $request->taluka_id,
                'address' => $request->address,
                'contact_number' => $request->contact_number,
                'contact_number' => $request->mobile,
                'email' => $request->email,
            ]);

            return redirect()->route('warehouse.index')->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {
            Log::error('Warehouse Update Error', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            return back()->with('error', 'Something went wrong.');
        }
    }


    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::find($id);

            if (!$warehouse) {
                return redirect()
                    ->back()
                    ->with('error', 'Warehouse not found.');
            }

            $hasChildren = Warehouse::where('parent_id', $id)->exists();

            if ($hasChildren) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete warehouse. Child warehouses exist.');
            }

            $warehouse->delete();

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {

            Log::error('Warehouse Delete Error', [
                'warehouse_id' => $id,
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while deleting warehouse.');
        }
    }

    public function indexWarehouse()
    {
        $stocks = WarehouseStock::with([
            'warehouse:id,name',
            'category:id,name',
            'product:id,name',
            'batch:id,batch_no'
        ])->paginate(10);

        return view('menus.warehouse.add-stock.index', compact('stocks'));
    }

    // add stock in warehouse
    public function addStockForm()
    {
        $mode = 'add';
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $product_batches = ProductBatch::all();

        return view('menus.warehouse.add-stock.add-stock', compact('mode', 'warehouses', 'categories', 'product_batches'));
    }

    public function addStock(Request $request)
    {
        // 🔹 Log request data
        Log::info('Add Stock Request', $request->all());

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'category_id'  => 'required|exists:categories,id',
            'product_id'   => 'required|exists:products,id',
            'batch_id'     => 'required|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            // 🔹 Check existing stock
            Log::info('Checking warehouse stock', [
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'batch_id'     => $request->batch_id,
            ]);

            $stock = WarehouseStock::where([
                'warehouse_id' => $request->warehouse_id,
                'category_id'  => $request->category_id,
                'product_id'   => $request->product_id,
                'batch_id'     => $request->batch_id,
            ])->first();

            if ($stock) {
                Log::info('Stock exists, updating quantity', [
                    'stock_id'     => $stock->id,
                    'old_quantity' => $stock->quantity,
                    'added_qty'    => $request->quantity,
                ]);

                $stock->quantity += $request->quantity;
                $stock->save();

                Log::info('Stock updated successfully', [
                    'new_quantity' => $stock->quantity,
                ]);
            } else {
                Log::info('Stock not found, creating new entry');

                $newStock = WarehouseStock::create([
                    'warehouse_id' => $request->warehouse_id,
                    'category_id'  => $request->category_id,
                    'product_id'   => $request->product_id,
                    'batch_id'     => $request->batch_id,
                    'quantity'     => $request->quantity,
                ]);

                Log::info('New stock created', $newStock->toArray());
            }

            DB::commit();

            Log::info('Add stock transaction committed successfully');

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock saved successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Add stock failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->all(),
            ]);

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('error', 'Something went wrong while saving stock');
        }
    }

    public function showStockForm($id)
    {
        $mode = 'view';
        $warehouse_stock = WarehouseStock::with(['warehouse', 'category', 'product', 'batch'])->findOrFail($id);
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();

        return view('menus.warehouse.add-stock.add-stock', compact(
            'mode',
            'warehouse_stock',
            'warehouses',
            'categories',
            'products',
            'product_batches'
        ));
    }

    public function editStockForm(Request $request, $id)
    {
        $mode = 'edit';
        $warehouse_stock = WarehouseStock::with(['warehouse', 'category', 'product', 'batch'])->findOrFail($id);
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products = Product::where('category_id', $warehouse_stock->category_id)->get();
        $product_batches = ProductBatch::where('product_id', $warehouse_stock->product_id)->get();

        return view('menus.warehouse.add-stock.add-stock', compact(
            'mode',
            'warehouse_stock',
            'warehouses',
            'categories',
            'products',
            'product_batches'
        ));
    }

    public function updateStock(Request $request, $id)
    {
        Log::info('Update Stock Request', array_merge(
            $request->all(),
            ['id' => $id]
        ));

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'category_id'  => 'required|exists:categories,id',
            'product_id'   => 'required|exists:products,id',
            'batch_id'     => 'required|exists:product_batches,id',
            'quantity'     => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();

        try {
            $stock = WarehouseStock::where('id', $id)->firstOrFail();

            $stock->update([
                'warehouse_id' => $request->warehouse_id,
                'category_id'  => $request->category_id,
                'product_id'   => $request->product_id,
                'batch_id'     => $request->batch_id,
                'quantity'     => $request->quantity,
            ]);

            DB::commit();

            return redirect()
                ->route('index.addStock.warehouse')
                ->with('success', 'Stock updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed');
        }
    }

    public function destroyStock(Request $request, $id)
    {

        Log::info('Delete Stock Request', $request->all());

        try {

            $stock = WarehouseStock::findOrFail($id);

            $stock->delete();

            Log::info('Warehouse stock soft deleted', [
                'stock_id' => $stock->id,
                'warehouse_id' => $stock->warehouse_id,
                'product_id' => $stock->product_id,
                'batch_id' => $stock->batch_id,
            ]);

            return redirect()
                ->back()
                ->with('success', 'Stock deleted successfully');
        } catch (\Throwable $e) {
            Log::error('Failed to delete stock', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unable to delete stock');
        }
    }

    public function getCategories($warehouseId)
    {
        $categories = Category::where('warehouse_id', $warehouseId)->get();

        return response()->json($categories);
    }
}
