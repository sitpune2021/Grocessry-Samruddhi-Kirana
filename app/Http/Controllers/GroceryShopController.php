<?php

namespace App\Http\Controllers;

use App\Models\GroceryShop;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroceryShopController extends Controller
{
    public function index()
    {
        return view('grocery_shops.index', [
            'shops' => GroceryShop::latest()->get()
        ]);
    }

    public function create()
    {
        return view('grocery_shops.create', [
            'shop' => null,
            'districtWarehouses' => Warehouse::whereNotNull('district_id')
                ->whereNull('taluka_id')
                ->orderBy('name')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'shop_name'             => 'required',
            'owner_name'            => 'required',
            'mobile_no'             => 'required|digits:10',
            'district_warehouse_id' => 'required|exists:warehouses,id',
            'taluka_id'             => 'required|exists:warehouses,id',
            'address'               => 'required',
        ]);

        DB::transaction(function () use ($request) {

            $districtWarehouse = Warehouse::find($request->district_warehouse_id);
            $talukaWarehouse   = Warehouse::find($request->taluka_id);

            $shop = GroceryShop::create([
                'shop_name'   => $request->shop_name,
                'owner_name'  => $request->owner_name,
                'mobile_no'   => $request->mobile_no,
                'address'     => $request->address,
                'district_id' => $districtWarehouse->district_id,
                'taluka_id'   => $talukaWarehouse->taluka_id,
                'status'      => 'active',
            ]);

            Warehouse::create([
                'grocery_shop_id' => $shop->id,
                'district_id'     => $districtWarehouse->district_id,
                'taluka_id'       => $talukaWarehouse->taluka_id,
                'type'            => 'shop',
                'status'          => 'active',
            ]);
        });

        return redirect()->route('grocery-shops.index')
            ->with('success', 'Shop created successfully');
    }
    public function edit(GroceryShop $groceryShop)
    {
        $districtWarehouseId = Warehouse::where('district_id', $groceryShop->district_id)
            ->whereNull('taluka_id')
            ->value('id');

        $talukaWarehouseId = Warehouse::where('taluka_id', $groceryShop->taluka_id)
            ->where('parent_id', $districtWarehouseId)
            ->whereNull('grocery_shop_id') // ONLY taluka warehouse
            ->value('id');

        return view('grocery_shops.create', [
            'shop' => $groceryShop,
            'districtWarehouses' => Warehouse::whereNotNull('district_id')
                ->whereNull('taluka_id')
                ->orderBy('name')
                ->get(),

            'selectedDistrict' => $districtWarehouseId,
            'selectedTaluka'   => $talukaWarehouseId,

            'isShow' => false
        ]);
    }


    public function update(Request $request, GroceryShop $groceryShop)
    {
        $request->validate([
            'shop_name' => 'required',
            'owner_name' => 'required',
            'mobile_no' => 'required|digits:10',
            'district_warehouse_id' => 'required|exists:warehouses,id',
            'taluka_id' => 'required|exists:warehouses,id',
            'address' => 'required',
        ]);

        $districtWarehouse = Warehouse::find($request->district_warehouse_id);
        $talukaWarehouse   = Warehouse::find($request->taluka_id);

        $groceryShop->update([
            'shop_name'   => $request->shop_name,
            'owner_name'  => $request->owner_name,
            'mobile_no'   => $request->mobile_no,
            'address'     => $request->address,
            'district_id' => $districtWarehouse->district_id,
            'taluka_id'   => $talukaWarehouse->taluka_id,
        ]);

        return redirect()->route('grocery-shops.index')
            ->with('success', 'Shop updated successfully');
    }
    public function show(GroceryShop $groceryShop)
    {
        // District warehouse id
        $districtWarehouseId = Warehouse::where('district_id', $groceryShop->district_id)
            ->whereNull('taluka_id')
            ->value('id');

        // Taluka warehouse id (IMPORTANT FIX)
        $talukaWarehouseId = Warehouse::where('taluka_id', $groceryShop->taluka_id)
            ->where('parent_id', $districtWarehouseId)
            ->whereNull('grocery_shop_id') // ⭐ taluka warehouse only
            ->value('id');

        return view('grocery_shops.create', [
            'shop' => $groceryShop,
            'districtWarehouses' => Warehouse::whereNotNull('district_id')
                ->whereNull('taluka_id')
                ->orderBy('name')
                ->get(),

            'selectedDistrict' => $districtWarehouseId,
            'selectedTaluka'   => $talukaWarehouseId,

            'isShow' => true
        ]);
    }


    // public function show(GroceryShop $groceryShop)
    // {
    //     return view('grocery_shops.create', [
    //         'shop' => $groceryShop,
    //         'districtWarehouses' => Warehouse::whereNotNull('district_id')
    //             ->whereNull('taluka_id')
    //             ->orderBy('name')
    //             ->get(),
    //         'selectedDistrict' => $groceryShop->districtWarehouse?->id,
    //         'selectedTaluka' => Warehouse::where('taluka_id', $groceryShop->taluka_id)
    //             ->whereNotNull('taluka_id')
    //             ->value('id'),
    //         // 'selectedTaluka' => $groceryShop->talukaWarehouse?->id,
    //         'isShow' => true
    //     ]);
    // }



    public function destroy(GroceryShop $groceryShop)
    {
        $groceryShop->delete();
        return back()->with('success', 'Shop deleted');
    }

    public function getTalukaWarehouses($districtWarehouseId)
    {
        return response()->json(
            Warehouse::where('parent_id', $districtWarehouseId)
                ->where('type', 'taluka')
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }
}
