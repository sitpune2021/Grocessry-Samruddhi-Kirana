<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    public function index()
    {
        // $offers = Offer::with(['product', 'category'])->paginate(10);
        return view('offers.index');
    }

    public function create()
    {
          $warehouses = Warehouse::all();
        return view('offers.create',compact('warehouses'))->with('mode', 'add');
    }

    public function store(Request $request) {}

    public function show()
    {
        return view('offers.create')->with('mode', 'show');
    }

    public function edit()
    {
        return view('offers.create')->with('mode', 'edit');
    }

    public function update() {}


    public function destroy() {}
}
