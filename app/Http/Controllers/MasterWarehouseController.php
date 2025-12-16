<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\MasterWarehouse;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        return view('menus.warehouse.master.add-warehouse', compact('mode', 'warehouses', 'categories', 'countries'));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'name'           => 'required|string|max:255',
                'type'           => 'required|in:master,district,taluka',
                'address'        => 'nullable|string|max:500',
                'contact_person' => 'nullable|string|max:255',
                'mobile'         => 'nullable|string|max:15',
                'email'          => 'nullable|email',


                'parent_id'  => 'required_if:type,district,required_if:type,taluka|nullable|integer',
                'district_id' => 'required_if:type,district,required_if:type,taluka|nullable|integer',
                'taluka_id'  => 'required_if:type,taluka|nullable|integer',
            ];
            $messages = [
                'parent_id.required_if'   => 'Parent warehouse is required.',
                'district_id.required_if' => 'District is required.',
                'taluka_id.required_if'   => 'Taluka is required.',
            ];

            $validated = $request->validate($rules, $messages);

            Log::info('Warehouse Store Request:', $request->all());

            $data = [
                'name'            => $request->name,
                'type'            => $request->type,
                'address'         => $request->address,
                'contact_person'  => $request->contact_person,
                'contact_number'  => $request->mobile,
                'email'          => $request->email,
                'country_id'          => $request->country_id,
                'state_id'          => $request->state_id,
                'status'          => 'active',
            ];

            if ($request->type == 'master') {
                $data['parent_id']  = null;
                $data['district_id'] = $request->district_id;;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'district') {
                $data['parent_id']   = $request->parent_id;   // master
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'taluka') {
                $data['parent_id']   = $request->parent_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = $request->taluka_id;
            }

            $warehouse = Warehouse::create($data);

            Log::info('Warehouse Created Successfully:', $warehouse->toArray());

            // if ($request->filled('email')) {

            //     $defaultPassword = 'Warehouse@123';

            //     User::create([
            //         'name'         => $request->name,
            //         'email'        => $request->email,
            //         'mobile'       => $request->mobile,
            //         'password'     => Hash::make($defaultPassword),
            //         'role_id'      => 2, // warehouse user role
            //         'warehouse_id' => $warehouse->id,
            //         'status'       => 'active',
            //     ]);
            // }

            return redirect()->route('warehouse.index')->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {

            Log::error('Warehouse Store Error:', [
                'error_message' => $e->getMessage(),
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
            ]);

            return back()->with('error', 'Something went wrong, please try again.');
        }
    }

    public function show($id)
    {
        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])->findOrFail($id);
            $countries = Country::all();

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'view', // view mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
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

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'edit', // edit mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
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
                'mobile' => 'nullable|string|max:15',
                'email' => 'nullable|email',
            ]);

            $warehouse->update([
                'name' => $request->name,
                'type' => $request->type,
                'parent_id' => $request->parent_id,
                'district_id' => $request->district_id,
                'taluka_id' => $request->taluka_id,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
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
}
