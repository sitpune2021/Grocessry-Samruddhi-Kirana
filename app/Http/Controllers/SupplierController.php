<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\District;
use App\Models\Talukas;
use App\Models\State;

class SupplierController extends Controller
{

    public function index()
    {
        $supplier = Supplier::orderBy('id', 'desc')->paginate(10);
        return view('supplier.index', compact('supplier'));
    }

    public function create()
    {
        $districts = District::all();
        $talukas   = Talukas::all();   
        $states    = State::all();

        return view('supplier.create', [
            'mode'      => 'add',
            'districts' => $districts,
            'talukas'   => $talukas,
            'states'    => $states,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'mobile'        => 'required|digits:10',
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('suppliers', $filename, 'public');
            $validated['logo'] = $filename;
        }

        Supplier::create($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier created successfully');
    }

    // VIEW
    public function show(string $id)
    {
        return view('supplier.create', [
            'supplier' => Supplier::findOrFail($id),
            'mode' => 'view'
        ]);
    }

    // EDIT
    public function edit(string $id)
    {
        return view('supplier.create', [
            'supplier' => Supplier::findOrFail($id),
            'mode' => 'edit'
        ]);
    }

    // UPDATE
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'mobile'        => 'required|digits:10',
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('suppliers', $filename, 'public');
            $validated['logo'] = $filename;
        }

        $supplier->update($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier updated successfully');
    }


    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return request()->wantsJson()
                ? response()->json(['status' => false, 'message' => 'Supplier not found'], 404)
                : redirect()->route('supplier.index')->with('error', 'Supplier not found');
        }

        try {
            $supplier->delete();

            return request()->wantsJson()
                ? response()->json(['status' => true, 'message' => 'Supplier deleted successfully'], 200)
                : redirect()->route('supplier.index')->with('success', 'Supplier deleted successfully');
        } catch (\Exception $e) {
            return request()->wantsJson()
                ? response()->json(['status' => false, 'message' => 'Failed to delete supplier', 'error' => $e->getMessage()], 500)
                : redirect()->route('supplier.index')->with('error', 'Failed to delete supplier. Please try again.');
        }
    }
}
