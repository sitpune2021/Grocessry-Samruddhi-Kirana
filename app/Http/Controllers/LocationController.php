<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Talukas;
use App\Models\UserAddress;
use App\Models\Warehouse;
use App\Models\WarehouseServicePincode;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function checkPincode(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        $service = WarehouseServicePincode::where('pincode', $request->pincode)->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Sorry, delivery not available at this location'
            ]);
        }

        session([
            'delivery_pincode' => $request->pincode,
            'dc_warehouse_id'  => $service->warehouse_id
        ]);

        return response()->json([
            'status' => true
        ]);
    }


    public function setPincode(Request $request)
    {
        $request->validate([
            'pincode' => 'required|digits:6'
        ]);

        $pincode = $request->pincode;

        // Set both DC and pincode in session
        $dc = Warehouse::where('type', 'distribution_center')
            ->where('status', 'active')
            ->whereHas('servicePincodes', fn($q) => $q->where('pincode', $pincode))
            ->first();
        session()->all();

        if ($dc) {
            session([
                'user_pincode' => $pincode,
                'dc_warehouse_id' => $dc->id
            ]);
        } else {
            // If no DC, just set pincode
            session(['user_pincode' => $pincode]);
            session()->forget('dc_warehouse_id');
        }

        return back();
    }
}
