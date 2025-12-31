<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\District;
use App\Models\Talukas;
use App\Models\State;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class SupplierController extends Controller
{

    public function index(Request $request)
    {
        $user = Auth::user();

        // Super Admin → All suppliers
        if ($user->role_id == 1) {
            $query = Supplier::with('warehouse');

            //Filter by warehouse from dropdown
            if ($request->filled('warehouse_id')) {
                $query->where('warehouse_id', $request->warehouse_id);
            }

            $suppliers = $query->latest()->paginate(10);
            $warehouses = Warehouse::select('id', 'name')->get();
        }
        // Warehouse Admin → Only own suppliers
        else {
            $suppliers = Supplier::with('warehouse')
                ->where('warehouse_id', $user->warehouse_id)
                ->latest()
                ->paginate(10);

            $warehouses = collect(); // empty
        }

        return view('supplier.index', compact('suppliers', 'warehouses'));
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
            'supplier'  => null,
            'states'    => State::select('id', 'name')->get(),
        ]);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'mobile'        => 'required|digits:10',
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string',
            'state_id'      => 'required|exists:states,id',
            'district_id'   => 'required|exists:districts,id',
            'taluka_id'     => 'required|exists:talukas,id',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bill_no'       => 'required|string',
            'challan_no'    => 'required|string',
            'batch_no'      => 'required|string',
        ]);

        $user = Auth::user();

        if (!$user || !$user->warehouse_id) {
            return redirect()->back()
                ->with('error', 'Warehouse not assigned to your account.');
        }

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('suppliers', $filename, 'public');
            $validated['logo'] = $filename;
        }

        $validated['warehouse_id'] = $user->warehouse_id;

        $supplier = Supplier::create($validated);

        // Log creation
        Log::info('Supplier created', [
            'id' => $supplier->id,
            'warehouse_id' => $supplier->warehouse_id,
            'name' => $supplier->supplier_name,
            'mobile' => $supplier->mobile,
            'state_id' => $supplier->state_id,
            'district_id' => $supplier->district_id,
            'taluka_id' => $supplier->taluka_id,
            'email' => $supplier->email,
        ]);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier created successfully');
    }

    // VIEW
    public function show(string $id)
    {
        return view('supplier.create', [
            'supplier' => Supplier::findOrFail($id),
            'mode' => 'view',
            'states'    => State::select('id', 'name')->get(),
            'districts' => District::select('id', 'name')->get(),
            'talukas'   => Talukas::select('id', 'name')->get()
        ]);
    }

    // EDIT
    public function edit(string $id)
    {
        return view('supplier.create', [
            'supplier' => Supplier::findOrFail($id),
            'mode' => 'edit',
            'states'    => State::select('id', 'name')->get(),
            'districts' => District::select('id', 'name')->get(),
            'talukas'   => Talukas::select('id', 'name')->get()
        ]);
    }

    // UPDATE
    public function update(Request $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'mobile'        => 'required|digits:10|unique:suppliers,mobile,' . $id,
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string',
            'state_id'      => 'required|exists:states,id',
            'district_id'   => 'required|exists:districts,id',
            'taluka_id'     => 'required|exists:talukas,id',
            'logo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'bill_no'       => 'required|string',
            'challan_no'    => 'required|string',
            'batch_no'      => 'required|string',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename =  $file->getClientOriginalExtension();
            $file->storeAs('suppliers', $filename, 'public');
            $validated['logo'] = $filename;
        }

        $supplier->update($validated);

        // Log update
        Log::info('Supplier updated', [
            'id' => $supplier->id,
            'name' => $supplier->supplier_name,
            'mobile' => $supplier->mobile,
            'state_id' => $supplier->state_id,
            'district_id' => $supplier->district_id,
            'taluka_id' => $supplier->taluka_id,
            'email' => $supplier->email,
        ]);

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
    public function getDistricts($stateId)
    {
        return District::where('state_id', $stateId)
            ->select('id', 'name')
            ->get();
    }

    public function getTalukas($districtId)
    {
        return Talukas::where('district_id', $districtId)
            ->select('id', 'name')
            ->get();
    }
}
