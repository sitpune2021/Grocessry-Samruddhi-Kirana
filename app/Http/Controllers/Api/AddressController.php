<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;

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

    public function list(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->get()
            ->map(function ($a) {
                return [
                    'id'           => $a->id,
                    'name'         => $a->first_name,
                    'mobile'       => $a->phone,
                    'address_line' => $a->address,
                    'landmark'     => $a->landmark, // ✅
                    'city'         => $a->city,
                    'state'        => $a->country,
                    'pincode'      => $a->postcode,
                    'latitude'     => $a->latitude,
                    'longitude'    => $a->longitude,
                    'type'         => (int) $a->type,
                    'is_default'   => (bool) $a->is_default,
                    'created_at'   => $a->created_at,
                    'updated_at'   => $a->updated_at
                ];
            });

        return response()->json([
            'status' => true,
            'data'   => $addresses
        ]);
    }


    public function add(Request $request)
    {
        $user = $request->user();

        // 🔐 Customer check
        if ($res = $this->checkCustomer($user)) return $res;

        // 🔢 Max 5 addresses per user
        $count = UserAddress::where('user_id', $user->id)->count();
        if ($count >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'You can add maximum 5 addresses only'
            ], 400);
        }

        // ✅ Validation
        $request->validate([
            'name'          => 'required|string',
            'mobile'        => 'required|digits:10',
            'address_line'  => 'required|string',
            'landmark'      => 'nullable|string',
            'city'          => 'required|string',
            'state'         => 'required|string',
            'pincode'       => 'required|digits:6',
            'type'          => 'required|in:1,2,3',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'is_default'    => 'nullable|in:0,1'
        ]);

        // ⭐ Default address handling
        if ($request->is_default == 1) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => 0]);
        }

        // 🏠 Create address
        $address = UserAddress::create([
            'user_id'    => $user->id,
            'first_name' => $request->name,
            'phone'      => $request->mobile,
            'address'    => $request->address_line,
            'landmark'   => $request->landmark,
            'city'       => $request->city,
            'country'    => $request->state,
            'postcode'   => $request->pincode,
            'email'      => $user->email,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'type'       => (int) $request->type, // ✅ saved correctly
            'is_default' => $request->is_default ?? 0
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();

        if ($res = $this->checkCustomer($user)) return $res;

        $address = UserAddress::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found'
            ], 404);
        }

        $request->validate([
            'name'         => 'required|string',
            'mobile'       => 'required|digits:10',
            'address_line' => 'required|string',
            'landmark'     => 'nullable|string',
            'city'         => 'required|string',
            'type'         => 'required|in:1,2,3',
            'state'        => 'required|string',
            'pincode'      => 'required|digits:6',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'is_default'   => 'nullable|in:0,1',
        ]);

        if ($request->is_default == 1) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => 0]);
        }

        $address->update([
            'first_name' => $request->name,
            'phone'      => $request->mobile,
            'address'    => $request->address_line,
            'landmark'     => $request->landmark,
            'city'       => $request->city,
            'country'    => $request->state,
            'postcode'   => $request->pincode,
            'email'      => $user->email,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'type'       => (int) $request->type,
            'is_default' => $request->is_default ?? $address->is_default,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address updated successfully',
            'data' => $address
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $address = UserAddress::where('id', $id)
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
