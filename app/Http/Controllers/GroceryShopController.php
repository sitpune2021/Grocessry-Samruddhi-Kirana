<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroceryShop;
use App\Models\Taluka;
use App\Models\District;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Warehouse;


class GroceryShopController extends Controller
{

    public function index()
    {
        return view('grocery_shops.index', [
            'shops'     => GroceryShop::latest()->get(),
            'shop'      => null,
            'districts' => District::orderBy('name')->get(),
        ]);
    }


    public function create()
    {
        $districtWarehouses = Warehouse::whereNotNull('district_id')
            ->whereNull('taluka_id')
            ->select('id', 'name', 'district_id')
            ->orderBy('name')
            ->get();
        return view('grocery_shops.create', [
            'shop'      => null,
            // 'districts' => District::orderBy('name')->get(),
            'districtWarehouses' => $districtWarehouses,
        ]);
    }


    public function store(Request $request)
    {
        Log::info('Grocery Shop Store Hit', $request->all());

        $request->validate([
            'shop_name'   => 'required|string|max:255',
            'owner_name'  => 'required|string|max:255',
            'mobile_no'   => 'required|digits:10', // match input name
            'district_id' => 'required|exists:districts,id',
            'taluka_id'   => 'required|exists:talukas,id',
            'address'     => 'required|string|max:500',
        ]);

        GroceryShop::create([
            'shop_name'   => $request->shop_name,
            'owner_name'  => $request->owner_name,
            'mobile_no'   => $request->mobile_no, // use input name here
            'address'     => $request->address,
            'district_id' => $request->district_id,
            'taluka_id'   => $request->taluka_id,
            'status'      => 'active',
        ]);

        return redirect()->route('grocery-shops.index')
            ->with('success', 'Shop added successfully');
    }


    public function edit(GroceryShop $groceryShop)
    {
        return view('grocery_shops.create', [
            'shop'      => $groceryShop,
            'districts' => District::orderBy('name')->get(),
        ]);
    }


    public function update(Request $request, GroceryShop $groceryShop)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
        ]);

        $groceryShop->update($request->all());

        return redirect()->route('grocery-shops.index')
            ->with('success', 'Shop updated successfully');
    }


    public function destroy(GroceryShop $groceryShop)
    {
        $groceryShop->delete();

        return back()->with('success', 'Shop deleted');
    }


    public function byDistrict($districtId)
    {
        $talukas = DB::table('talukas')
            ->where('district_id', $districtId)
            ->orderBy('name')
            ->get();

        return response()->json($talukas);
    }


    public function show(GroceryShop $groceryShop)
    {
        // District (model hai)
        $district = $groceryShop->district;

        // Taluka (model nahi hai → direct table query)
        $taluka = DB::table('talukas')
            ->where('id', $groceryShop->taluka_id)
            ->first();

        return view('grocery_shops.show', [
            'shop'     => $groceryShop,
            'district' => $district,
            'taluka'   => $taluka,
        ]);
    }


    public function getTalukaWarehouses($district_warehouse_id)
    {
        $talukaWarehouses = Warehouse::where('parent_id', $district_warehouse_id)
            ->where('type', 'taluka')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
// dd($talukaWarehouses);
        return response()->json($talukaWarehouses);
    }
}
