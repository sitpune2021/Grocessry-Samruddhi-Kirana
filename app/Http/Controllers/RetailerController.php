<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;

class RetailerController extends Controller
{
    public function index()
    {
        $retailers = Retailer::latest()->paginate(10);
        return view('retailers.index', compact('retailers'));
    }

    public function create()
    {
        $retailer = null; 
        return view('retailers.form', compact('retailer'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:retailers,email',
            'mobile'  => 'required|unique:retailers,mobile',
            'address' => 'nullable|string',
        ]);

        Retailer::create($data);

        return redirect()->route('retailers.index')
            ->with('success', 'Retailer created successfully');
    }

    public function edit(Retailer $retailer)
    {
        return view('retailers.form', compact('retailer'));
    }


    public function update(Request $request, Retailer $retailer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:retailers,email,' . $retailer->id,
            'mobile'  => 'required|unique:retailers,mobile,' . $retailer->id,
            'address' => 'nullable|string',
        ]);

        $retailer->update($data);

        return redirect()->route('retailers.index')
            ->with('success', 'Retailer updated successfully');
    }

    public function destroy(Retailer $retailer)
    {
        $retailer->delete();

        return back()->with('success', 'Retailer deleted');
    }

    public function toggleStatus(Retailer $retailer)
    {
        $retailer->update([
            'is_active' => !$retailer->is_active
        ]);

        return back()->with('success', 'Status updated');
    }



}
