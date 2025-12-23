<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\ProductBatch;
use App\Models\Product;

class FIFOHistoryController extends Controller
{
    public function index()
    {
        $sellProducts = StockMovement::with([
            'warehouse',
            'batch.product.category'
        ])->orderBy('id', 'desc')->get(); // or orderBy('created_at', 'desc')

        return view('fifo-history.index', compact('sellProducts'));
    }
}
