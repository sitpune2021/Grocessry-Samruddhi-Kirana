<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrderReturn;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerOrderReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $returns = CustomerOrderReturn::with([
            'customer',
            'order',
            'product',
            'orderItem'
        ])->latest()->get();
        return view('menus.customer-management.customer-return.index', compact('returns'));
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

        $return = CustomerOrderReturn::with([
            'customer',
            'order',
            'product'
        ])->findOrFail($id);

        return view('menus.customer-management.customer-return.view', compact('return'));
    }

    // QC PAGE
    public function edit($id)
    {
        $return = CustomerOrderReturn::with([
            'customer',
            'order',
            'product'
        ])->findOrFail($id);

        return view('menus.customer-management.customer-return.qc', compact('return'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'qc_status' => 'required|in:passed,failed,partial',
            'status'    => 'required|in:approved,rejected',
        ]);

        $return = CustomerOrderReturn::findOrFail($id);

        $return->update([
            'qc_status'  => $request->qc_status,
            'status'     => $request->status,
            'received_at' => Carbon::now(),
        ]);

        // ✅ OPTIONAL: Stock update logic
        if ($request->qc_status === 'passed') {
            // add quantity back to inventory
        }

        return redirect()
            ->route('customer-returns.index')
            ->with('success', 'QC updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
