<?php

namespace App\Http\Controllers;

use App\Models\DistrictWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DistrictWarehouseController extends Controller
{
    public function index()
    {
        return DistrictWarehouse::with('talukaWarehouses')->get();
    }

 public function store(Request $request)
{
    // Log request data
    Log::info('District Warehouse Store Request', $request->all());

    // Validate input
    $validated = $request->validate([
        'state_id' => 'required|exists:states,id',
        'name'     => 'required|string|max:255',
    ]);

    // Create record
    $data = DistrictWarehouse::create($validated);

    // Log created data
    Log::info('District Warehouse Created', $data->toArray());

    // Return response
    return response()->json([
        'status'  => true,
        'message' => 'District warehouse created successfully',
        'data'    => $data
    ], 201);
}



    public function show($id)
    {
        return DistrictWarehouse::with('talukaWarehouses')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $warehouse = DistrictWarehouse::findOrFail($id);
        $warehouse->update($request->all());
        return $warehouse;
    }

    public function destroy($id)
    {
        return DistrictWarehouse::destroy($id);
    }
}
