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
use App\Models\Talukas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role_id == 1) {

            $warehouses = Warehouse::orderBy('id', 'desc')
                ->paginate(20);
        }
      
        elseif ($user->warehouse->type === 'master') {

            $masterWarehouseId = $user->warehouse_id;

          
            $districtIds = Warehouse::where('type', 'district')
                ->where('parent_id', $masterWarehouseId)
                ->pluck('id');

          
            $talukaIds = Warehouse::where('type', 'taluka')
                ->whereIn('parent_id', $districtIds)
                ->pluck('id');

            $shopIds = Warehouse::where('type','distribution_center')
                ->whereIn('parent_id',$talukaIds)
                ->pluck('id');

            
            $allowedWarehouseIds = collect([$masterWarehouseId])
                ->merge($districtIds)
                ->merge($talukaIds)
                ->merge($shopIds);

            $warehouses = Warehouse::whereIn('id', $allowedWarehouseIds)
                ->orderBy('id', 'desc')
                ->paginate(20);
        }
    
        elseif ($user->warehouse->type === 'district') {

            $districtWarehouseId = $user->warehouse_id;

            $talukaIds = Warehouse::where('type', 'taluka')
                ->where('parent_id', $districtWarehouseId)
                ->pluck('id');

            $allowedWarehouseIds = collect([$districtWarehouseId])
                ->merge($talukaIds);

            $warehouses = Warehouse::whereIn('id', $allowedWarehouseIds)
                ->orderBy('id', 'desc')
                ->paginate(20);
        }
        else {
            $warehouses = Warehouse::where('id', $user->warehouse_id)
                ->paginate(20);
        }

        return view(
            'menus.warehouse.master.index',
            compact('warehouses')
        );
    }


    public function create()
    {
        $mode = 'add';
        $warehouses = Warehouse::with(['district','taluka'])->get();
        $categories = Category::all();
        $countries = Country::all();
        $districts = District::orderBy('name')->get();
        $talukas = Talukas::all();


        return view('menus.warehouse.master.add-warehouse', compact('mode', 'warehouses', 'categories', 'countries', 'districts','talukas'));
    }



    public function store(Request $request)
    {
       
        DB::beginTransaction();
        $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'type' => 'required|in:master,district,taluka,distribution_center',
            'contact_person' => 'nullable|string|min:3|max:50',
            'email' => 'nullable|email',
            'contact_number' => [
                'nullable',
                'regex:/^[6-9]\d{9}$/',
                Rule::unique('warehouses', 'contact_number'),
            ],
            'parent_id' => 'nullable|required_if:type,district|required_if:type,taluka|required_if:type,distribution_center|integer',
            'district_id' => 'required_if:type,district|required_if:type,taluka|integer',
            'taluka_id'   => 'required_if:type,taluka|integer',
            'address'     => 'required|string|max:500',
        ], [
            'contact_number.regex'  => 'Please enter a valid 10-digit mobile number starting with 6-9',
            'contact_number.unique' => 'This mobile number is already registered.',
        ]);



        try {

            $warehouse = Warehouse::create([
                'name'           => $request->name,
                'type'           => $request->type,
                'parent_id'      => $request->parent_id,
                'district_id'    => $request->district_id,
                'taluka_id'      => $request->taluka_id,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email'          => $request->email,
                'address'        => $request->address,
            ]);

            /* 👤 SPLIT NAME */
            // $nameParts = preg_split('/\s+/', trim($request->contact_person));
            // $firstName = $nameParts[0];
            // $lastName  = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;

            // /* 👤 CREATE USER */
            // $user = User::create([
            //     'first_name'  => $firstName,
            //     'last_name'   => $lastName,
            //     'email'       => $request->email,
            //     'password'    => Hash::make('Warehouse@123'),
            //     'mobile'      => $request->contact_number,
            //     // 'warehouse_id'=> $warehouse->id,
            //     'status'      => 1,
            // ]);

            DB::commit();

            Log::info('Warehouse & user created', [
                'warehouse_id' => $warehouse->id,
                // 'user_id'      => $user->id,
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse & user created successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Warehouse store failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }

    public function show($id)
    {
        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])->findOrFail($id);
            $countries = Country::all();
            $districts = District::all();
            $talukas = Talukas::all();

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'view', // view mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
                'districts' => $districts,
                'talukas' => $talukas,

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
        Log::info('Warehouse edit page opened', ['warehouse_id' => $id]);

        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])
                ->findOrFail($id);

            return view('menus.warehouse.master.add-warehouse', [
                'mode'       => 'edit',
                'warehouse'  => $warehouse,
                'countries'  => Country::all(),
                'warehouses' => Warehouse::all(),
                'districts'  => District::all(),
                'talukas'    => Talukas::all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse edit failed', [
                'warehouse_id' => $id,
                'message'      => $e->getMessage()
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Warehouse update request received', [
            'warehouse_id' => $id,
            'request_data' => $request->except(['_token']),
            'user_id'      => auth()->id(),
        ]);

        try {

            $warehouse = Warehouse::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:master,district,taluka,distribution_center',
                'address' => 'nullable|string|max:500',
                //     'contact_person' => 'nullable|string|max:255',
                //     'contact_number' => [
                //         'required',
                //         'regex:/^[6-9]\d{9}$/',
                //         Rule::unique('warehouses', 'contact_number')->ignore($id),
                //     ],
                //     'email' => 'nullable|email',
                // ], [
                //     'contact_number.regex'  => 'Please enter a valid 10-digit mobile number starting with 6-9',
                //     'contact_number.unique' => 'This mobile number is already registered.',
            ]);

            Log::info('Warehouse update validation passed', [
                'warehouse_id' => $id,
                'validated'    => $validated,
            ]);

            // Optional hierarchy fields
            $validated['parent_id']   = $request->parent_id ?? null;
            $validated['district_id'] = $request->district_id ?? null;
            $validated['taluka_id']   = $request->taluka_id ?? null;

            DB::transaction(function () use ($warehouse, $validated) {

                // Update Warehouse
                $warehouse->update($validated);

                Log::info('Warehouse updated successfully', [
                    'warehouse_id' => $warehouse->id,
                    'updated_data' => $validated,
                ]);

                // Update linked user (warehouse admin / incharge)
                $user = User::where('warehouse_id', $warehouse->id)->first();

                if ($user) {
                    $user->update([
                        'email'  => $validated['email'] ?? $user->email,
                        'mobile' => $validated['contact_number'] ?? $user->mobile,
                    ]);

                    Log::info('Linked warehouse user updated', [
                        'warehouse_id' => $warehouse->id,
                        'user_id'      => $user->id,
                    ]);
                } else {
                    Log::warning('No linked user found for warehouse', [
                        'warehouse_id' => $warehouse->id,
                    ]);
                }
            });

            Log::info('Warehouse update process completed', [
                'warehouse_id' => $id,
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse updated successfully.');
        } catch (\Throwable $e) {

            Log::error('Warehouse update failed', [
                'warehouse_id' => $id,
                'error'        => $e->getMessage(),
                'line'         => $e->getLine(),
                'file'         => $e->getFile(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update warehouse. Please try again.');
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
