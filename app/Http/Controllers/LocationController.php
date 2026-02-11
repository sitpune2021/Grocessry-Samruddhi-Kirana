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
                'delivery_pincode' => $pincode,
                'dc_warehouse_id' => $dc->id
            ]);
        } else {
            // If no DC, just set pincode
            session(['user_pincode' => $pincode]);
            session()->forget('dc_warehouse_id');
        }

        return back();
    }

    public function saveAddress(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'User not logged in'
            ], 401);
        }
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'flat_house' => 'required',
            'area'       => 'required',
            'city'       => 'required',
            'postcode'   => 'required|digits:6',
            'phone'      => 'required|digits:10',
            'type'       => 'required|in:1,2,3',
            'address_id' => 'nullable|exists:user_addresses,id'
        ]);

        $address = UserAddress::where('user_id', auth()->id())
            ->where('type', $request->type)
            ->first();

        if ($address) {
            // UPDATE existing
            $address->update($request->except('_token'));
        } else {
            // CREATE new
            $address = UserAddress::create([
                'user_id'    => auth()->id(),
                'type'       => $request->type,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'flat_house' => $request->flat_house,
                'floor'      => $request->floor,
                'area'       => $request->area,
                'landmark'   => $request->landmark,
                'city'       => $request->city,
                'postcode'   => $request->postcode,
                'phone'      => $request->phone,
                'is_default' => 1
            ]);
        }


        // 🔥 Header + checkout session
        session([
            'delivery_address' => [
                'id'    => $address->id,
                'title' => ['1' => 'Home', '2' => 'Work', '3' => 'Other'][$address->type],
                'area'  => $address->area,
                'postcode' => $address->postcode,
            ]
        ]);

        return response()->json([
            'status' => true,
            'address' => $address
        ]);
    }
}
