<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\State;
use App\Models\Talukas;
use Illuminate\Http\Request;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function checkByPincode(Request $request)
    {
        $request->validate([
            'postcode' => 'required|digits:6'
        ]);

        $pincode = $request->postcode;

        // 🔹 Taluka find by pincode (example logic)
        $taluka = Talukas::where('pincode', $pincode)->first();

        if (!$taluka) {
            return response()->json([
                'status' => false,
                'message' => 'We do not deliver to this pincode'
            ]);
        }

        // 🔹 Warehouses check
        $warehouses = Warehouse::where('type', 'distribution_center')
            ->where('taluka_id', $taluka->id)
            ->where('status', 'active')
            ->exists();

        if (!$warehouses) {
            return response()->json([
                'status' => false,
                'message' => 'Currently not serviceable'
            ]);
        }

        // 🔥 SESSION save (guest + user)
        session([
            'selected_pincode' => $pincode,
            'selected_taluka'  => $taluka->name,
            'taluka_id'        => $taluka->id
        ]);

        // 🔥 LOGIN asel tar DB madhe save
        if (Auth::check()) {
            UserAddress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'type' => 1
                ],
                [
                    'postcode' => $pincode,
                    'city'     => $taluka->name,
                    'country'  => 'India'
                ]
            );
        }

        return response()->json([
            'status' => true,
            'pincode' => $pincode,
            'taluka' => $taluka->name
        ]);
    }

    public function getStates($countryId)
    {

        return response()->json(
            State::where('country_id', $countryId)->get()
        );
    }

    public function getDistricts($stateId)
    {
        return response()->json(
            District::where('state_id', $stateId)->get()
        );
    }

    public function getTalukas($districtId)
    {
        return response()->json(
            Talukas::where('district_id', $districtId)->get()
        );
    }
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
