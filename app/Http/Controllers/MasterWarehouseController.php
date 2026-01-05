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
use Illuminate\Validation\Rule;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::orderBy('id', 'desc')->paginate(10);
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
        $request->validate(
            [
                'name' => 'required|string|max:255|unique:warehouses,name',
                'type' => 'required|in:master,district,taluka',
                'contact_person' => 'required|string|min:3|max:50',
                'email' => 'required|email',
                'contact_number' => [
                    'required',
                    'regex:/^[6-9]\d{9}$/',
                    Rule::unique('warehouses', 'contact_number'),
                ],

                'parent_id' => 'nullable|required_if:type,district|required_if:type,taluka|integer',
                'district_id' => 'nullable|required_if:type,district|required_if:type,taluka|integer',
                'taluka_id' => 'nullable|required_if:type,taluka|integer',
                'address'  => 'required|string|max:500'
            ],
            [
                'contact_number.regex'  => 'Please enter a valid 10-digit mobile number starting with 6-9',
                'contact_number.unique' => 'This mobile number is already registered.',
            ]
        );


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
            $data['district_id'] = $request->district_id;;
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
        $warehouse = Warehouse::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:master,district,taluka',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_number' => [
                'required',
                'regex:/^[6-9]\d{9}$/',
                Rule::unique('warehouses', 'contact_number')->ignore($id),
            ],
            'email' => 'nullable|email',
        ], [
            'contact_number.regex'  => 'Please enter a valid 10-digit mobile number starting with 6-9',
            'contact_number.unique' => 'This mobile number is already registered.',
        ]);

        // Add optional fields
        $validated['parent_id'] = $request->parent_id ?? null;
        $validated['district_id'] = $request->district_id ?? null;
        $validated['taluka_id'] = $request->taluka_id ?? null;

        $warehouse->update($validated);

        return redirect()->route('warehouse.index')
            ->with('success', 'Warehouse updated successfully.');
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
}
