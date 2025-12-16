<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\State;
use App\Models\Talukas;
use Illuminate\Http\Request;

class LocationController extends Controller
{

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
