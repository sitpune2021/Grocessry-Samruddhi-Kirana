<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taxes = Tax::orderBy('id', 'desc')->paginate(20);
        return view('menus.taxes.tax-index', compact('taxes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = "add";
        return view('menus.taxes.tax-create', compact('mode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $request->validate([
                'name'      => 'required|string|max:100|unique:taxes,name',
                'cgst'      => 'required|numeric|min:0|max:100',
                'sgst'      => 'required|numeric|min:0|max:100',
                'igst'      => 'nullable|numeric|min:0|max:100',
                'gst'       => 'required|numeric|min:0|max:100',
                'is_active' => 'required|in:0,1',
            ]);
        try {

            DB::beginTransaction();

            $tax = Tax::create([
                'name'      => $request->name,
                'cgst'      => $request->cgst,
                'sgst'      => $request->sgst,
                'igst'      => $request->igst,
                'gst'      => $request->gst,
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            Log::info('Tax created successfully', [
                'tax_id' => $tax->id,
                'name'   => $tax->name,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('taxes.index')
                ->with('success', 'Tax added successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Tax validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);

            throw $e; 

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error while creating tax', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'input'   => $request->all(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mode = "show";
        $tax  = Tax::findOrFail($id);
        return view('menus.taxes.tax-create', compact('mode', 'tax'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mode = "edit";
        $tax  = Tax::findOrFail($id);
        return view('menus.taxes.tax-create', compact('mode', 'tax'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $request->validate([
                'name'      => 'required|string|max:100|unique:taxes,name,' . $id,
                'cgst'      => 'required|numeric|min:0|max:100',
                'sgst'      => 'required|numeric|min:0|max:100',
                'igst'      => 'nullable|numeric|min:0|max:100',
                'gst'      => 'required|numeric|min:0|max:100',

                'is_active' => 'required|in:0,1',
            ]);

            DB::beginTransaction();

            $tax = Tax::findOrFail($id);

            $tax->update([
                'name'      => $request->name,
                'cgst'      => $request->cgst,
                'sgst'      => $request->sgst,
                'igst'      => $request->igst,
                'gst'      => $request->gst,
                'is_active' => $request->is_active,
            ]);

            DB::commit();

            Log::info('Tax updated successfully', [
                'tax_id'  => $tax->id,
                'user_id' => auth()->id(),
                'data'    => $request->only(['name', 'cgst', 'sgst', 'igst', 'is_active']),
            ]);

            return redirect()
                ->route('taxes.index')
                ->with('success', 'Tax updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {

            Log::warning('Tax update validation failed', [
                'tax_id' => $id,
                'errors' => $e->errors(),
            ]);

            throw $e;
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error while updating tax', [
                'tax_id' => $id,
                'message' => $e->getMessage(),
                'file'   => $e->getFile(),
                'line'   => $e->getLine(),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating tax.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
