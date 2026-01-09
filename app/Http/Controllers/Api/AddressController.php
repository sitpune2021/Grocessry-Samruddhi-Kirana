<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use Validator;
class AddressController extends Controller
{
    public function list(Request $request)
    {
        $addresses = Addresse::where('user_id', $request->user()->id)
            ->orderByDesc('is_default')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }

    // ✅ Add address
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required|digits:10',
            'address_line' => 'required',
            'city' => 'required',
            'state' => 'required',
            'pincode' => 'required|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Handle default address
        if ($request->is_default == 1) {
            DeliveryAddress::where('user_id', $request->user()->id)
                ->update(['is_default' => 0]);
        }

        $address = DeliveryAddress::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'mobile' => $request->mobile,
            'address_line' => $request->address_line,
            'landmark' => $request->landmark,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_default' => $request->is_default ?? 0
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);
    }

    // ✅ Update address
    public function update(Request $request, $id)
    {
        $address = DeliveryAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ]);
        }

        if ($request->is_default == 1) {
            DeliveryAddress::where('user_id', $request->user()->id)
                ->update(['is_default' => 0]);
        }

        $address->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    // ✅ Delete address
    public function delete(Request $request, $id)
    {
        $address = DeliveryAddress::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ]);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}
