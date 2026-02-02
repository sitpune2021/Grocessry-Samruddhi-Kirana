<?php

namespace App\Http\Controllers;

use App\Models\OnSaleProduct;
use App\Models\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    public function create(Request $request)
    {
        $batch = ProductBatch::with('product')
            ->where('id', $request->batch_id)
            ->where('warehouse_id', Auth::user()->warehouse_id)
            ->where('quantity', '>', 0)
            ->whereDate('expiry_date', '>=', now())
            ->firstOrFail();

        $daysLeft = now()->diffInDays($batch->expiry_date);

        return view('sale.create', compact('batch', 'daysLeft'));
    }

    public function store(Request $request)
    {
        Log::info('Near-expiry sale attempt started', [
            'user_id' => Auth::id(),
            'request' => $request->all(),
        ]);

        $userWarehouseId = Auth::user()->warehouse_id;

        try {

            $request->validate([
                'product_batch_id' => 'required|exists:product_batches,id',
                'discount_percent' => 'required|integer|min:1|max:80',
                'sale_end_date'    => 'required|date|after_or_equal:today',
            ]);

            $batch = ProductBatch::with('product')
                ->where('id', $request->product_batch_id)
                ->where('warehouse_id', $userWarehouseId)
                ->first();

            if (!$batch) {
                Log::warning('Batch not found or warehouse mismatch', [
                    'batch_id' => $request->product_batch_id,
                    'user_warehouse' => $userWarehouseId,
                ]);

                return back()
                    ->with('error', 'Invalid batch or warehouse mismatch.');
            }

            /* ---------- Sale end date check ---------- */

            $lastAllowedSaleDate = \Carbon\Carbon::parse($batch->expiry_date)->subDay();

            if (\Carbon\Carbon::parse($request->sale_end_date)->gt($lastAllowedSaleDate)) {

                Log::warning('Invalid sale end date (after expiry)', [
                    'batch_id' => $batch->id,
                    'expiry_date' => $batch->expiry_date,
                    'sale_end_date' => $request->sale_end_date,
                ]);

                return back()
                    ->withErrors([
                        'sale_end_date' => 'Sale end date must be before the expiry date.'
                    ])
                    ->withInput();
            }

            /* ---------- SAFETY CHECKS ---------- */

            if ($batch->expiry_date < now()) {
                return back()->withErrors([
                    'discount_percent' => 'This batch is already expired.'
                ]);
            }

            if ($batch->quantity <= 0) {
                return back()->withErrors([
                    'discount_percent' => 'No stock available for this batch.'
                ]);
            }

            if (
                OnSaleProduct::where('product_batch_id', $batch->id)
                ->where('status', 'active')
                ->exists()
            ) {
                return back()->withErrors([
                    'discount_percent' => 'This batch is already on online sale.'
                ]);
            }

            /* ---------- PRICE CHECK ---------- */
            $mrp        = (float) $batch->product->mrp;
            $selling    = (float) $batch->product->final_price;
            $basePrice  = (float) $batch->product->base_price;

            // Discount on SELLING PRICE
            $discountAmount = $selling * ($request->discount_percent / 100);
            $salePrice      = $selling - $discountAmount;

            if ($salePrice < $basePrice) {
                return back()
                    ->withErrors([
                        'discount_percent' => 'Discount too high. Sale price cannot be below base price.'
                    ])
                    ->withInput();
            }


            /* ---------- SAVE ---------- */

            $sale = OnSaleProduct::create([
                'product_id'        => $batch->product_id,
                'product_batch_id'  => $batch->id,
                'warehouse_id'      => $userWarehouseId,

                'mrp'               => $mrp,
                'original_price'    => $selling, // selling price before sale
                'sale_price'        => round($salePrice, 2),
                'discount_percent'  => $request->discount_percent,
                'discount_amount'   => round($discountAmount, 2),

                'sale_start_date'   => now(),
                'sale_end_date'     => $request->sale_end_date,

                'channel'           => 'online',
                'status'            => 'active',
            ]);



            Log::info('Near-expiry sale created successfully', [
                'sale_id' => $sale->id,
                'batch_id' => $batch->id,
                'warehouse_id' => $userWarehouseId,
                'created_by' => Auth::id(),
            ]);

            return redirect()
                ->route('batches.expiry')
                ->with('success', 'Product successfully added to online sale');
        } catch (\Throwable $e) {

            Log::error('Near-expiry sale failed unexpectedly', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }
}
