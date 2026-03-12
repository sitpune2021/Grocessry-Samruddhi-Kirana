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
use App\Models\WarehouseServicePincode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class MasterWarehouseController extends Controller
{


    // public function index()
    // {
    //     $user = auth()->user();

    //     if ($user->role_id == 1) {

    //         $warehouses = Warehouse::orderBy('id', 'desc')
    //             ->paginate(20);
    //     }

    //     elseif ($user->warehouse->type === 'master') {

    //         $masterWarehouseId = $user->warehouse_id;


    //         $districtIds = Warehouse::where('type', 'district')
    //             ->where('parent_id', $masterWarehouseId)
    //             ->pluck('id');


    //         $talukaIds = Warehouse::where('type', 'taluka')
    //             ->whereIn('parent_id', $districtIds)
    //             ->pluck('id');

    //         $shopIds = Warehouse::where('type','distribution_center')
    //             ->whereIn('parent_id',$talukaIds)
    //             ->pluck('id');


    //         $allowedWarehouseIds = collect([$masterWarehouseId])
    //             ->merge($districtIds)
    //             ->merge($talukaIds)
    //             ->merge($shopIds);

    //         $warehouses = Warehouse::whereIn('id', $allowedWarehouseIds)
    //             ->orderBy('id', 'desc')
    //             ->paginate(20);
    //     }

    //     elseif ($user->warehouse->type === 'district') {

    //         $districtWarehouseId = $user->warehouse_id;

    //         $talukaIds = Warehouse::where('type', 'taluka')
    //             ->where('parent_id', $districtWarehouseId)
    //             ->pluck('id');

    //         $allowedWarehouseIds = collect([$districtWarehouseId])
    //             ->merge($talukaIds);

    //         $warehouses = Warehouse::whereIn('id', $allowedWarehouseIds)
    //             ->orderBy('id', 'desc')
    //             ->paginate(20);
    //     }
    //     else {
    //         $warehouses = Warehouse::where('id', $user->warehouse_id)
    //             ->paginate(20);
    //     }

    //     return view(
    //         'menus.warehouse.master.index',
    //         compact('warehouses')
    //     );
    // }


    public function index()
    {
        $user = auth()->user();

        if ($user->role_id == 1) {

            $warehouses = Warehouse::with('users') // 👈 YAHAN ADD
                ->orderBy('id', 'desc')
                ->paginate(20);
        } elseif ($user->warehouse->type === 'master') {

            $masterWarehouseId = $user->warehouse_id;

            $districtIds = Warehouse::where('type', 'district')
                ->where('parent_id', $masterWarehouseId)
                ->pluck('id');

            $talukaIds = Warehouse::where('type', 'taluka')
                ->whereIn('parent_id', $districtIds)
                ->pluck('id');

            $shopIds = Warehouse::where('type', 'distribution_center')
                ->whereIn('parent_id', $talukaIds)
                ->pluck('id');

            $allowedWarehouseIds = collect([$masterWarehouseId])
                ->merge($districtIds)
                ->merge($talukaIds)
                ->merge($shopIds);

            $warehouses = Warehouse::with('users') // 👈 YAHAN ADD
                ->whereIn('id', $allowedWarehouseIds)
                ->orderBy('id', 'desc')
                ->paginate(20);
        } elseif ($user->warehouse->type === 'district') {

            $districtWarehouseId = $user->warehouse_id;

            $talukaIds = Warehouse::where('type', 'taluka')
                ->where('parent_id', $districtWarehouseId)
                ->pluck('id');

            $allowedWarehouseIds = collect([$districtWarehouseId])
                ->merge($talukaIds);

            $warehouses = Warehouse::with('users') // 👈 YAHAN ADD
                ->whereIn('id', $allowedWarehouseIds)
                ->orderBy('id', 'desc')
                ->paginate(20);
        } else {

            $warehouses = Warehouse::with('users') // 👈 YAHAN ADD
                ->where('id', $user->warehouse_id)
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
        $warehouses = Warehouse::with(['district', 'taluka'])->get();
        $categories = Category::all();
        $countries = Country::all();
        $districts = District::orderBy('name')->get();
        $talukas = Talukas::all();


        return view('menus.warehouse.master.add-warehouse', compact('mode', 'warehouses', 'categories', 'countries', 'districts', 'talukas'));
    }

    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $response = Http::withHeaders([
            'User-Agent' => 'SamruddhiKirana/1.0 (admin@gmail.com)'
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat'    => $request->lat,
            'lon'    => $request->lng,
        ]);

        if ($response->failed()) {
            return response()->json(['pincode' => null]);
        }

        return response()->json([
            'pincode' => $response->json('address.postcode')
        ]);
    }


    public function store(Request $request)
    {
        Log::info('Warehouse Store Request Started', [
            'request_data' => $request->all()
        ]);

        DB::beginTransaction();

        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:warehouses,name',
                'type' => 'required|in:master,district,taluka,distribution_center',
                'parent_id' => 'nullable|required_if:type,district|required_if:type,taluka|required_if:type,distribution_center|integer',
                'district_id' => 'required_if:type,district|required_if:type,taluka|integer',
                'taluka_id'   => 'required_if:type,taluka|integer',
                'address'     => 'required|string|max:500',
                'pincode'     => 'nullable',
                'latitude'    => 'nullable',
                'longitude'   => 'nullable',
                'service_radius_km' => 'nullable',
            ]);

            Log::info('Warehouse Validation Passed', [
                'validated_data' => $validated
            ]);

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
                'latitude'       => $request->latitude,
                'longitude'      => $request->longitude,
                'service_radius_km' => $request->service_radius_km,
            ]);

            Log::info('Warehouse Created Successfully', [
                'warehouse_id' => $warehouse->id,
                'type' => $warehouse->type
            ]);

            if ($request->type === 'distribution_center') {

                Log::info('Creating Distribution Center Pincode', [
                    'warehouse_id' => $warehouse->id,
                    'pincode' => $request->pincode
                ]);

                WarehouseServicePincode::create([
                    'warehouse_id' => $warehouse->id,
                    'pincode'      => $request->pincode,
                ]);

                Log::info('Pincode Created Successfully');
            }

            DB::commit();

            Log::info('Warehouse Store Transaction Committed', [
                'warehouse_id' => $warehouse->id
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse & user created successfully');
        } catch (\Illuminate\Validation\ValidationException $ve) {

            Log::warning('Warehouse Validation Failed', [
                'errors' => $ve->errors()
            ]);

            DB::rollBack();
            throw $ve;
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Warehouse Store Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
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

            $servicePincode = WarehouseServicePincode::where('warehouse_id', $warehouse->id)->first();

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'view', // view mode
                'warehouse' => $warehouse,
                'servicePincode' =>  $servicePincode,
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

            $servicePincode = WarehouseServicePincode::where('warehouse_id', $warehouse->id)->first();

            return view('menus.warehouse.master.add-warehouse', [
                'mode'       => 'edit',
                'warehouse'  => $warehouse,
                'servicePincode' =>  $servicePincode,
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
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);

        try {

            $warehouse = Warehouse::findOrFail($id);

            Log::info('Warehouse found', [
                'warehouse_id' => $warehouse->id,
                'name' => $warehouse->name
            ]);

            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('warehouses', 'name')->ignore($warehouse->id),
                ],
                'type' => 'required|in:master,district,taluka,distribution_center',
                'parent_id' => 'nullable|required_if:type,district|required_if:type,taluka|required_if:type,distribution_center|integer',
                'district_id' => 'required_if:type,district|required_if:type,taluka|integer',
                'taluka_id' => 'required_if:type,taluka|integer',
                'address' => 'required|string|max:500',

                // DC only
                'pincode' => 'nullable|required_if:type,distribution_center|digits:6',
                'latitude' => 'required_if:type,distribution_center|numeric',
                'longitude' => 'required_if:type,distribution_center|numeric',
                'service_radius_km' => 'required_if:type,distribution_center|integer|min:1',
            ]);

            Log::info('Warehouse validation passed', [
                'warehouse_id' => $id
            ]);

            DB::transaction(function () use ($request, $warehouse) {

                Log::info('Updating warehouse', [
                    'warehouse_id' => $warehouse->id
                ]);

                // Update warehouse
                $warehouse->update([
                    'name' => $request->name,
                    'type' => $request->type,
                    'parent_id' => $request->parent_id,
                    'district_id' => $request->district_id,
                    'taluka_id' => $request->taluka_id,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'service_radius_km' => $request->service_radius_km,
                ]);

                Log::info('Warehouse updated successfully', [
                    'warehouse_id' => $warehouse->id
                ]);

                // Handle DC pincode
                if ($request->type === 'distribution_center') {

                    WarehouseServicePincode::updateOrCreate(
                        ['warehouse_id' => $warehouse->id],
                        ['pincode' => $request->pincode]
                    );

                    Log::info('Distribution center pincode updated', [
                        'warehouse_id' => $warehouse->id,
                        'pincode' => $request->pincode
                    ]);
                } else {

                    WarehouseServicePincode::where('warehouse_id', $warehouse->id)->delete();

                    Log::info('Distribution center pincode removed', [
                        'warehouse_id' => $warehouse->id
                    ]);
                }
            });

            Log::info('Warehouse update transaction completed', [
                'warehouse_id' => $id
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse updated successfully');
        } catch (\Exception $e) {

            Log::error('Warehouse update failed', [
                'warehouse_id' => $id,
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while updating warehouse.');
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

    public function getTalukas($districtId)
    {
        $talukas = Talukas::where('district_id', $districtId)->get();

        return response()->json($talukas);
    }
}
