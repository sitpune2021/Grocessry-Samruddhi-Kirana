<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

use Validator;

class AddressController extends Controller
{
    private function checkCustomer($user)
    {
        if (!$user->role || strtolower($user->role->name) !== 'customer') {
            return response()->json([
                'status' => false,
                'message' => 'Only customers can manage addresses'
            ], 403);
        }
        return null;
    }

    // 📄 List addresses
    public function list(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }

    // ➕ Add address
    public function add(Request $request)
    {
        $user = $request->user();

        // 🔐 Customer role check
        if ($res = $this->checkCustomer($user)) return $res;

        // ✅ Validation (NO Validator facade)
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|digits:10',
            'address_line' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'pincode' => 'required|digits:6'
        ]);

        // ⭐ Handle default address
        if ($request->is_default == 1) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => 0]);
        }

        // ✅ Create address
        $address = Address::create([
            'user_id' => $user->id,
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

    // ✏️ Update address
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $address = Address::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        if ($request->is_default == 1) {
            Address::where('user_id', $user->id)
                ->update(['is_default' => 0]);
        }

        $address->update($request->only([
            'name',
            'mobile',
            'address_line',
            'landmark',
            'city',
            'state',
            'pincode',
            'latitude',
            'longitude',
            'is_default'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    // ❌ Delete address
    public function delete(Request $request, $id)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $address = Address::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully'
        ]);
    }
}
