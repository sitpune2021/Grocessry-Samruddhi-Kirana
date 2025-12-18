<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Batch;
use App\Models\User;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryCount = Category::count();
        $ProductCount = Product::count();
        $BatchCount = Batch::count();
        $WarehouseCount = Warehouse::count();
        $StockMovementCount = WarehouseStock::count();
        $WarehouseTransferCount = WarehouseTransfer::count();
        $UserCount = User::count();

        return view(
            'dashboard.dashboard',
            compact(
                'categoryCount',
                'ProductCount',
                'BatchCount',
                'WarehouseCount',
                'StockMovementCount',
                'WarehouseTransferCount',
                'UserCount'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
