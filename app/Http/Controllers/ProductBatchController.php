<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;


class ProductBatchController extends Controller
{

    public function index()
    {
        $batches = ProductBatch::with('product')->get();
        return view('batches.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::all();
        return view('batches.create', compact('products'));
    }


    public function store(Request $request)
    {
        try {

            // ✅ Validation
            $validated = $request->validate([
                'product_id'  => 'required|exists:products,id',
                'batch_no'    => 'required',
                'mfg_date'    => 'nullable|date',
                'expiry_date' => 'nullable|date|after:mfg_date',
                'quantity'    => 'required|integer|min:1',
            ]);

            // ✅ Create Product Batch
            $batch = ProductBatch::create([
                'product_id'  => $validated['product_id'],
                'batch_no'    => $validated['batch_no'],
                'mfg_date'    => $validated['mfg_date'] ?? null,
                'expiry_date' => $validated['expiry_date'] ?? null,
                'quantity'    => $validated['quantity'],
            ]);

            // ✅ Stock Movement Entry
            StockMovement::create([
                'product_batch_id' => $batch->id,
                'type'             => 'in',
                'quantity'         => $validated['quantity'],
            ]);

            // ✅ Success log (optional but useful)
            Log::info('Product batch created successfully', [
                'batch_id'  => $batch->id,
                'product_id'=> $validated['product_id'],
                'quantity'  => $validated['quantity'],
            ]);

            return redirect('/batches')->with('success', 'Batch added successfully');

        }
        catch (ValidationException $e) {

            // ❌ Validation error log
            Log::warning('Product batch validation failed', [
                'errors' => $e->errors(),
                'input'  => $request->all(),
            ]);

            throw $e; // Laravel ko validation error handle karne do
        }
        catch (Exception $e) {

            // ❌ Any other error (DB, logic, etc.)
            Log::error('Error while creating product batch', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'input'   => $request->all(),
            ]);

            return back()->with('error', 'Something went wrong while saving batch');
        }
    }


    public function expiryAlerts()
    {
        $batches = ProductBatch::where('quantity', '>', 0)
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->get();

        return view('batches.expiry', compact('batches'));
    }

}
