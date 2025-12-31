<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Category;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportsController extends Controller
{

    public function warehouse_stock_report(Request $request)
    {
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $products   = Product::all();

        $stocks = WarehouseStock::with(['warehouse', 'product', 'category'])
            ->when($request->warehouse_id, function ($q) use ($request) {
                $q->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->category_id, function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            })
            ->when($request->product_id, function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->paginate(20)
            ->withQueryString(); // 🔥 keep filters on pagination

        return view(
            'reports.warehouse-stock',
            compact('stocks', 'warehouses', 'categories', 'products')
        );
    }

    public function index()
    {
        //
    }



    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }


    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
