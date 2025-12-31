<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    public function index()
{
    Log::info('Coupon index accessed', [
        'user_id' => Auth::id()
    ]);

    $coupons = Coupon::orderByDesc('id')->paginate(10);

    Log::info('Coupons fetched', [
        'total' => $coupons->total()
    ]);

    return view('coupons.index', compact('coupons'));
}


    public function create()
{
    Log::info('Coupon create page opened', [
        'user_id' => Auth::id()
    ]);

    $mode = 'add';
    return view('coupons.create', compact('mode'));
}

    public function store(Request $request)
{
    Log::info('Coupon store request received', [
        'request_data' => $request->except(['_token'])
    ]);

    $request->validate([
        'code' => 'required|string|unique:coupons,code',
        'type' => 'required|in:flat,percent,free_shipping',
        'value' => 'required|numeric|min:0',
        'min_cart_amount' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'usage_limit' => 'nullable|integer|min:1',
        'per_user_limit' => 'nullable|integer|min:1',
        'is_active' => 'required|in:0,1',
    ]);

    try {
        $coupon = Coupon::create($request->all());

        Log::info('Coupon created successfully', [
            'coupon_id' => $coupon->id,
            'code' => $coupon->code,
            'user_id' => Auth::id()
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon created successfully.');

    } catch (\Exception $e) {

        Log::error('Coupon creation failed', [
            'error' => $e->getMessage(),
            'request_data' => $request->all()
        ]);

        return back()->with('error', 'Something went wrong');
    }
}

   public function show(Coupon $coupon)
{
    Log::info('Coupon viewed', [
        'coupon_id' => $coupon->id,
        'user_id' => Auth::id()
    ]);

    $mode = 'view';
    return view('coupons.create', compact('coupon', 'mode'));
}


    public function edit(Coupon $coupon)
{
    Log::info('Coupon edit page opened', [
        'coupon_id' => $coupon->id,
        'user_id' => Auth::id()
    ]);

    $mode = 'edit';
    return view('coupons.create', compact('coupon', 'mode'));
}

public function update(Request $request, Coupon $coupon)
{
    Log::info('Coupon update request received', [
        'coupon_id' => $coupon->id,
        'request_data' => $request->except(['_token', '_method'])
    ]);

    $request->validate([
        'code' => 'required|string|unique:coupons,code,' . $coupon->id,
        'type' => 'required|in:flat,percent,free_shipping',
        'value' => 'required|numeric|min:0',
        'min_cart_amount' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'usage_limit' => 'nullable|integer|min:1',
        'per_user_limit' => 'nullable|integer|min:1',
        'is_active' => 'required|in:0,1',
    ]);

    try {
        $coupon->update($request->all());

        Log::info('Coupon updated successfully', [
            'coupon_id' => $coupon->id,
            'user_id' => Auth::id()
        ]);

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon updated successfully.');

    } catch (\Exception $e) {

        Log::error('Coupon update failed', [
            'coupon_id' => $coupon->id,
            'error' => $e->getMessage()
        ]);

        return back()->with('error', 'Update failed');
    }
}


    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('coupons.index')
            ->with('success', 'Coupon deleted successfully');
    }

}
