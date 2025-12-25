<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GroceryShop;

class GroceryShopController extends Controller
{
  public function index()
{
    $shops = GroceryShop::latest()->get();

    return view('grocery_shops.index', [
        'shops' => $shops,
        'shop'  => null, // create mode by default
    ]);
}

public function create()
{
    return view('grocery_shops.create', [
        'shop' => null
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'shop_name' => 'required|string|max:255',
        'mobile_no' => 'nullable|digits:10',
    ]);

    GroceryShop::create($request->all());

    return redirect()->route('grocery-shops.index')
        ->with('success', 'Shop added successfully');
}
public function edit(GroceryShop $groceryShop)
{
    return view('grocery_shops.create', [
        'shop' => $groceryShop
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


}
