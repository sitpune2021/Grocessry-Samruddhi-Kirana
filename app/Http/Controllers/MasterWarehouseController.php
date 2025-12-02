<?php

namespace App\Http\Controllers;

use App\Models\MasterWarehouse;
use Illuminate\Http\Request;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        return MasterWarehouse::with('districtWarehouses')->get();
    }

    public function store(Request $request)
    {
        return MasterWarehouse::create($request->all());
    }

    public function show($id)
    {
        return MasterWarehouse::with('districtWarehouses')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $warehouse = MasterWarehouse::findOrFail($id);
        $warehouse->update($request->all());
        return $warehouse;
    }

    public function destroy($id)
    {
        return MasterWarehouse::destroy($id);
    }
}
