<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierChallan;

class SupplierChallenController extends Controller
{

    public function index()
    {
        $challans = SupplierChallan::with([
            'purchaseOrder',
            'supplier',
            'warehouse'
        ])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('supplier_challan.index', compact('challans'));
    }

    public function create()
    {
        return view('supplier_challan.create');
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
