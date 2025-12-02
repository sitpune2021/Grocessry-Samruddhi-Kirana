<?php

namespace App\Http\Controllers;

use App\Models\DistrictWarehouse;
use Illuminate\Http\Request;

class DistrictWarehouseController extends Controller
{
    public function index()
    {
        return DistrictWarehouse::with('talukaWarehouses')->get();
    }

    public function store(Request $request)
    {
        // return DistrictWarehouse::create($request->all());

        $validated = $request->validate([
        'master_warehouse_id' => 'required|exists:master_warehouses,id',
        'name' => 'required|string',
        'location' => 'required|string'
    ]);

    return DistrictWarehouse::create($validated);
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
