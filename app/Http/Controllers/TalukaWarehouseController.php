<?php

namespace App\Http\Controllers;

use App\Models\TalukaWarehouse;
use Illuminate\Http\Request;

class TalukaWarehouseController extends Controller
{
    public function index()
    {
        return TalukaWarehouse::all();
    }

    public function store(Request $request)
    {
        // return TalukaWarehouse::create($request->all());
        $validated = $request->validate([
        'district_warehouse_id' => 'required|exists:district_warehouses,id',
        'name' => 'required|string',
        'location' => 'required|string'
    ]);

    return TalukaWarehouse::create($validated);
    }

    public function show($id)
    {
        return TalukaWarehouse::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $warehouse = TalukaWarehouse::findOrFail($id);
        $warehouse->update($request->all());
        return $warehouse;
    }

    public function destroy($id)
    {
        return TalukaWarehouse::destroy($id);
    }
}
