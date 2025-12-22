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
use App\Models\ProductBatch;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     return view('dashboard.dashboard');
    // }

    public function index()
    {
        $categoryCount = Category::count();
        $ProductCount = Product::count();
        $BatchCount = Batch::count();
        $WarehouseCount = Warehouse::count();
        $StockMovementCount = WarehouseStock::count();
        $WarehouseTransferCount = WarehouseTransfer::count();
        $UserCount = User::count();

        $expiredCount = ProductBatch::where('quantity', '>', 0)
            ->whereDate('expiry_date', '<', now())
            ->count();

        $expiringSoonCount = ProductBatch::where('quantity', '>', 0)
            ->whereBetween('expiry_date', [
                now(),
                now()->addDays(7)
            ])->count();

        return view(
            'dashboard.dashboard',
            compact(
                'categoryCount',
                'ProductCount',
                'BatchCount',
                'WarehouseCount',
                'StockMovementCount',
                'WarehouseTransferCount',
                'UserCount',
                'expiredCount',
                'expiringSoonCount'
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
