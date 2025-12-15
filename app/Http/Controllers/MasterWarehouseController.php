<?php

namespace App\Http\Controllers;

use App\Models\MasterWarehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        return Warehouse::with('districtWarehouses')->get();
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'name'            => 'required|string|max:255',
                'type'            => 'required|in:master,district,taluka',
                'address'         => 'nullable|string|max:500',
                'contact_person'  => 'nullable|string|max:255',
                'mobile'          => 'nullable|string|max:15',

                'master_id'              => 'required_if:type,district|nullable|integer',
                'district_id'            => 'required_if:type,district,required_if:type,taluka|nullable|integer',
                'district_warehouse_id'  => 'required_if:type,taluka|nullable|integer',
                'taluka_id'              => 'required_if:type,taluka|nullable|integer',
            ];

            $messages = [
                'master_id.required_if'             => 'Master warehouse is required for district warehouse.',
                'district_id.required_if'           => 'District is required.',
                'district_warehouse_id.required_if' => 'District warehouse is required for taluka warehouse.',
                'taluka_id.required_if'             => 'Taluka is required.',
            ];

            $validated = $request->validate($rules, $messages);

            Log::info('Warehouse Store Request:', $request->all());

            $data = [
                'name'            => $request->name,
                'type'            => $request->type,
                'address'         => $request->address,
                'contact_person'  => $request->contact_person,
                'mobile'          => $request->mobile,
                'status'          => 'active',
            ];

            if ($request->type == 'master') {
                $data['parent_id']  = null;
                $data['district_id'] = null;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'district') {
                $data['parent_id']   = $request->master_id;   // master
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'taluka') {
                $data['parent_id']   = $request->district_warehouse_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = $request->taluka_id;
            }

            $warehouse = Warehouse::create($data);

            Log::info('Warehouse Created Successfully:', $warehouse->toArray());

            return back()->with('success', 'Warehouse created successfully.');
        } catch (\Exception $e) {

            Log::error('Warehouse Store Error:', [
                'error_message' => $e->getMessage(),
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
            ]);

            return back()->with('error', 'Something went wrong, please try again.');
        }
    }


    public function show($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            Log::info('Warehouse Show:', $warehouse->toArray());

            return view('warehouse.show', compact('warehouse'));
        } catch (\Exception $e) {

            Log::error('Warehouse Show Error:', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }


    public function update(Request $request, $id)
    {
        try {

            $rules = [
                'name'            => 'required|string|max:255',
                'type'            => 'required|in:master,district,taluka',
                'address'         => 'nullable|string|max:500',
                'contact_person'  => 'nullable|string|max:255',
                'mobile'          => 'nullable|string|max:15',

                'master_id'              => 'required_if:type,district|nullable|integer',
                'district_id'            => 'required_if:type,district,required_if:type,taluka|nullable|integer',
                'district_warehouse_id'  => 'required_if:type,taluka|nullable|integer',
                'taluka_id'              => 'required_if:type,taluka|nullable|integer',
            ];

            $messages = [
                'master_id.required_if'             => 'Master warehouse is required for district warehouse.',
                'district_id.required_if'           => 'District is required.',
                'district_warehouse_id.required_if' => 'District warehouse is required for taluka warehouse.',
                'taluka_id.required_if'             => 'Taluka is required.',
            ];

            $request->validate($rules, $messages);

            Log::info('Warehouse Update Request:', $request->all());

            $warehouse = Warehouse::findOrFail($id);

            $data = [
                'name'            => $request->name,
                'type'            => $request->type,
                'address'         => $request->address,
                'contact_person'  => $request->contact_person,
                'mobile'          => $request->mobile,
            ];

            if ($request->type == 'master') {
                $data['parent_id']  = null;
                $data['district_id'] = null;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'district') {
                $data['parent_id']   = $request->master_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type == 'taluka') {
                $data['parent_id']   = $request->district_warehouse_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = $request->taluka_id;
            }

            $warehouse->update($data);

            Log::info('Warehouse Updated Successfully:', $warehouse->toArray());

            return back()->with('success', 'Warehouse updated successfully.');
        } catch (\Exception $e) {

            Log::error('Warehouse Update Error:', [
                'id' => $id,
                'error_message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return back()->with('error', 'Something went wrong while updating warehouse.');
        }
    }



    public function destroy($id)
    {
        return Warehouse::destroy($id);
    }
}
