<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->orderByDesc('is_default') // ⭐ default first
            ->get()
            ->map(function ($a) {
                return [
                    'id'           => $a->id,
                    'name'         => $a->first_name,
                    'mobile'       => $a->phone,
                    'flat_no'      => $a->flat_house,
                    'floor'        =>$a->floor,
                    'building_area'=>$a->area,
                    'landmark'     => $a->landmark,
                    'city'         => $a->city,
                    'pincode'      => $a->postcode,
                    'latitude'     => $a->latitude,
                    'longitude'    => $a->longitude,
                    'type'         => (int) $a->type,
                    'is_default'   => (bool) $a->is_default
                ];
            });


        return response()->json([
            'status' => true,
            'data'   => $addresses
        ]);
    }

    public function add(Request $request)
{
    try {
        Log::info('Address Add API called', [
            'user_id' => optional($request->user())->id,
            'request' => $request->all()
        ]);

        $user = $request->user();

        if ($res = $this->checkCustomer($user)) {
            Log::warning('Customer check failed', ['user_id' => $user->id]);
            return $res;
        }

        $count = UserAddress::where('user_id', $user->id)->count();

        Log::info('User address count', [
            'user_id' => $user->id,
            'count' => $count
        ]);

        if ($count >= 5) {
            Log::warning('Address limit reached', ['user_id' => $user->id]);

            return response()->json([
                'status' => false,
                'message' => 'You can add maximum 5 addresses only'
            ], 400);
        }

        $validated = $request->validate([
            'name'         => 'required|string',
            'mobile'       => 'required|digits:10',
            'flat_no'      => 'required|string',
            'floor'        => 'required|string',
            'building_area'=> 'required|string',
            'landmark'     => 'nullable|string',
            'city'         => 'required|string',
            
            'pincode'      => 'required|digits:6',
            'type'         => 'required|in:1,2,3',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'is_default'   => 'nullable|boolean'
        ]);

        Log::info('Validation passed', $validated);

        // ⭐ Decide default
        $isDefault = false;

        if ($count === 0) {
            $isDefault = true;
            Log::info('First address - auto default');
        } elseif ($request->is_default === true) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => false]);

            $isDefault = true;

            Log::info('User selected default - resetting previous');
        }

        $address = UserAddress::create([
            'user_id'    => $user->id,
            'first_name' => $request->name,
            'phone'      => $request->mobile,
            'flat_house'    => $request->flat_no,
            'floor'      =>$request->floor,
            'area'      =>$request->building_area,
            'landmark'   => $request->landmark,
            'city'       => $request->city,
            'country'    => $request->state,
            'postcode'   => $request->pincode,
            'email'      => $user->email,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'type'       => (int) $request->type,
            'is_default' => $isDefault
        ]);

        Log::info('Address created successfully', [
            'address_id' => $address->id,
            'user_id' => $user->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Address added successfully',
            'data' => $address
        ]);

    } catch (\Exception $e) {

        Log::error('Address Add API Error', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);

        return response()->json([
            'status' => false,
            'message' => 'Internal server error'
        ], 500);
    }
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
            'flat_no'      => 'required|string',
            'floor'        => 'required|string',
            'building_area'=> 'required|string',
            'landmark'     => 'nullable|string',
            'city'         => 'required|string',
            'type'         => 'required|in:1,2,3',
            'state'        => 'required|string',
            'pincode'      => 'required|digits:6',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'is_default'   => 'nullable|boolean'
        ]);

        // ⭐ If making this default → reset others
        if ($request->is_default === true) {
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'first_name' => $request->name,
            'phone'      => $request->mobile,
            'flat_house'    => $request->flat_no,
            'floor'      =>$request->floor,
            'area'      =>$request->building_area,
            'landmark'   => $request->landmark,
            'city'       => $request->city,
            'country'    => $request->state,
            'postcode'   => $request->pincode,
            'email'      => $user->email,
            'latitude'   => $request->latitude,
            'longitude'  => $request->longitude,
            'type'       => (int) $request->type,
            'is_default' => $request->has('is_default')
                ? (bool) $request->is_default
                : $address->is_default
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
    public function setDefault(Request $request)
    {
        $user = $request->user();
        if ($res = $this->checkCustomer($user)) return $res;

        $request->validate([
            'address_id' => 'required|exists:user_addresses,id'
        ]);

        // Check address belongs to user
        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'status' => false,
                'message' => 'Address not found or does not belong to this user'
            ], 400);
        }


        DB::transaction(function () use ($user, $address) {

            // Remove previous default
            UserAddress::where('user_id', $user->id)
                ->update(['is_default' => false]);

            // Set new default
            $address->update([
                'is_default' => true
            ]);
        });

        return response()->json([
            'status' => true,
            'message' => 'Default address updated successfully',
            'data' => [
                'address_id' => $address->id
            ]
        ]);
    }
}
