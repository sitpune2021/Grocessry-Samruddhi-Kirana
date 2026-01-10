<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Unit;

class BatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_no' => 'required|string|max:50',
            'expiry_date' => 'required|date',
            'unit'        => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $batch = Batch::create($request->all());

        // Log entry
        Log::info('New Batch Created', [
            'batch_id'   => $batch->id,
            'product_id' => $batch->product_id,
            'batch_no'   => $batch->batch_no,
            'expiry_date' => $batch->expiry_date,
            'quantity'   => $batch->quantity,

        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Batch created successfully',
            'data'    => $batch
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $batch = Batch::find($id);

        if (!$batch) {

            // Log for not found case
            Log::warning('Batch Not Found', [
                'batch_id' => $id,

            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Batch not found',
            ], 404);
        }

        // Log for successful fetch
        Log::info('Batch Fetched', [
            'batch_id'    => $batch->id,
            'product_id'  => $batch->product_id,
            'batch_no'    => $batch->batch_no,
            'expiry_date' => $batch->expiry_date,
            'quantity'    => $batch->quantity,

        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Batch fetched successfully',
            'data'    => $batch
        ], 200);
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
    public function update(Request $request, $id)
    {
        $batch = Batch::find($id);

        if (!$batch) {

            Log::warning('Batch Update Failed - Not Found', [
                'batch_id' => $id,
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Batch not found',
            ], 404);
        }

        // Validation
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_no'   => 'required|string|max:50',
            'expiry_date' => 'required|date',
            'quantity'   => 'required|integer|min:1',
            'purchase_price' => 'required|numeric',
            'mrp' => 'required|numeric',
        ]);

        // Update fields
        $batch->update($request->only([
            'product_id',
            'batch_no',
            'expiry_date',
            'quantity',
            'purchase_price',
            'mrp'
        ]));

        // Log success
        Log::info('Batch Updated Successfully', [
            'batch_id'       => $batch->id,
            'product_id'     => $batch->product_id,
            'batch_no'       => $batch->batch_no,
            'expiry_date'    => $batch->expiry_date,
            'quantity'       => $batch->quantity,
            'purchase_price' => $batch->purchase_price,
            'mrp'            => $batch->mrp,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Batch updated successfully',
            'data'    => $batch
        ], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $batch = Batch::find($id);

        if (!$batch) {
            // Log not found
            Log::warning('Batch Delete Failed - Not Found', [
                'batch_id' => $id,
                'ip'       => request()->ip(),
                'time'     => now()->toDateTimeString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Batch not found',
            ], 404);
        }

        // Delete batch
        $batch->delete();

        // Log success
        Log::info('Batch Deleted Successfully', [
            'batch_id'    => $batch->id,
            'product_id'  => $batch->product_id,
            'batch_no'    => $batch->batch_no,
            'expiry_date' => $batch->expiry_date,
            'quantity'    => $batch->quantity,
            'purchase_price' => $batch->purchase_price ?? null,
            'mrp'            => $batch->mrp ?? null,

            'ip'          => request()->ip(),
            'deleted_at'  => now()->toDateTimeString(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Batch deleted successfully'
        ], 200);
    }
}
