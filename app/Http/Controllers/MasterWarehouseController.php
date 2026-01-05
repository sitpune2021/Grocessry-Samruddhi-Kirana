<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\MasterWarehouse;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\District;

class MasterWarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::orderBy('id', 'desc')->paginate(10);
        return view('menus.warehouse.master.index', compact('warehouses'));
    }
    public function create()
    {
        $mode = 'add';
        $warehouses = Warehouse::all();
        $categories = Category::all();
        $countries = Country::all();
        $districts = District::orderBy('name')->get();


        return view('menus.warehouse.master.add-warehouse', compact('mode', 'warehouses', 'categories', 'countries', 'districts'));
    }

    //     public function store(Request $request)
    //     {
    //       $request->validate([
    //     'name' => 'required|string|max:255|unique:warehouses,name',
    //     'type' => 'required|in:master,district,taluka',
    //     'contact_person' => 'required|string|min:3|max:50',
    //     'email' => 'required|email',
    //     'contact_number' => 'required|digits:10',
    //     'parent_id' => 'nullable|required_if:type,district|required_if:type,taluka|integer',
    //     'district_id' => 'nullable|required_if:type,district|required_if:type,taluka|integer',
    //     'taluka_id' => 'nullable|required_if:type,taluka|integer',
    //     'address'  => 'required|string|max:500'
    // ]);


    //         $data = [
    //             'name'           => $request->name,
    //             'type'           => $request->type,
    //             'address'        => $request->address,
    //             'contact_person' => $request->contact_person,
    //             'contact_number' => $request->contact_number,
    //             'email'          => $request->email,
    //             'country_id'     => $request->country_id,
    //             'state_id'       => $request->state_id,
    //             'status'         => 'active',
    //         ];

    //         if ($request->type === 'master') {
    //             $data['parent_id']   = null;
    //             $data['district_id'] = $request->district_id;;
    //             $data['taluka_id']   = null;
    //         }

    //         if ($request->type === 'district') {
    //             $data['parent_id']   = $request->parent_id;
    //             $data['district_id'] = $request->district_id;
    //             $data['taluka_id']   = null;
    //         }

    //         if ($request->type === 'taluka') {
    //             $data['parent_id']   = $request->parent_id;
    //             $data['district_id'] = $request->district_id;
    //             $data['taluka_id']   = $request->taluka_id;
    //         }

    //         Warehouse::create($data);

    //         User::create([
    //             'first_name' => 'Samruddh',
    //             'last_name' => 'Kirana',
    //             'email' => 'admin@gmail.com',
    //             'password' => Hash::make('Admin@123'),
    //             'role_id' => 1,
    //             'mobile' => 9503654539,
    //             'warehouse_id' => $warehouse->id
    //         ]);

    //         return redirect()->route('warehouse.index')
    //             ->with('success', 'Warehouse created successfully');
    //     }



    public function store(Request $request)
    {
        Log::info('Warehouse store request received', [
            'email' => $request->email,
            'type'  => $request->type
        ]);

        $request->validate([
            'name'            => 'required|string|max:255|unique:warehouses,name',
            'type'            => 'required|in:master,district,taluka',
            'contact_person'  => 'required|string|min:3|max:50',
            'email'           => 'required|email|unique:users,email',
            'contact_number'  => 'required|digits:10',
            'parent_id'       => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'district_id'     => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'taluka_id'       => 'nullable|required_if:type,taluka|integer',
            'address'         => 'required|string|max:500',
        ]);

        Log::info('Warehouse validation passed', [
            'name' => $request->name
        ]);

        DB::beginTransaction();

        try {
            /* ======================
           Prepare Warehouse Data
        ====================== */
            $data = [
                'name'           => $request->name,
                'type'           => $request->type,
                'address'        => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email'          => $request->email,
                'country_id'     => $request->country_id,
                'state_id'       => $request->state_id,
                'status'         => 'active',
            ];

            if ($request->type === 'master') {
                $data['parent_id']   = null;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type === 'district') {
                $data['parent_id']   = $request->parent_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type === 'taluka') {
                $data['parent_id']   = $request->parent_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = $request->taluka_id;
            }

            Log::info('Creating warehouse', $data);

            /* ======================
           Create Warehouse
        ====================== */
            Warehouse::create($data);


            /* ======================
           Split Contact Person Name
        ====================== */
            $fullName  = trim($request->contact_person);
            $nameParts = preg_split('/\s+/', $fullName);

            $firstName = $nameParts[0];
            $lastName  = count($nameParts) > 1
                ? implode(' ', array_slice($nameParts, 1))
                : null;

            Log::info('User name split', [
                'full_name' => $fullName,
                'first'     => $firstName,
                'last'      => $lastName
            ]);

            /* ======================
           Create User
        ====================== */
            $user = User::create([
                'first_name'   => $firstName,
                'last_name'    => $lastName,
                'email'        => $request->email,
                'password'     => Hash::make('Warehouse@123'),
                'mobile'       => $request->contact_number,
                'status'       => 1
            ]);

            Log::info('Warehouse user created successfully', [
                'user_id'      => $user->id,
            ]);

            DB::commit();

            Log::info('Warehouse store transaction committed', [
                'warehouse_id' => Warehouse::latest()->first()->id
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse & user created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Warehouse store failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'error' => 'Something went wrong. Please try again.'
            ])->withInput();
        }
    }



    public function show($id)
    {
        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])->findOrFail($id);
            $countries = Country::all();
            $districts = District::all();   // 🔹 this was missing

            return view('menus.warehouse.master.add-warehouse', [
                'mode' => 'view', // view mode
                'warehouse' => $warehouse,
                'countries' => $countries,
                'warehouses' => Warehouse::all(),
                'districts' => $districts,

            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse Show Error', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }

    public function edit($id)
    {
        Log::info('Warehouse edit page opened', ['warehouse_id' => $id]);

        try {
            $warehouse = Warehouse::with(['parent', 'country', 'state', 'district', 'taluka'])
                ->findOrFail($id);

            return view('menus.warehouse.master.add-warehouse', [
                'mode'       => 'edit',
                'warehouse'  => $warehouse,
                'countries'  => Country::all(),
                'warehouses' => Warehouse::all(),
                'districts'  => District::all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Warehouse edit failed', [
                'warehouse_id' => $id,
                'message'      => $e->getMessage()
            ]);

            return back()->with('error', 'Warehouse not found.');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Warehouse update request received', [
            'warehouse_id' => $id
        ]);

        $request->validate([
            'name'            => 'required|string|max:255|unique:warehouses,name,' . $id,
            'type'            => 'required|in:master,district,taluka',
            'contact_person'  => 'required|string|min:3|max:50',
            'email'           => 'required|email',
            'contact_number'  => 'required|digits:10',
            'parent_id'       => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'district_id'     => 'nullable|required_if:type,district|required_if:type,taluka|integer',
            'taluka_id'       => 'nullable|required_if:type,taluka|integer',
            'address'         => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $warehouse = Warehouse::findOrFail($id);

            /* ======================
           Prepare Warehouse Data
        ====================== */
            $data = [
                'name'           => $request->name,
                'type'           => $request->type,
                'address'        => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email'          => $request->email,
            ];

            if ($request->type === 'master') {
                $data['parent_id']   = null;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type === 'district') {
                $data['parent_id']   = $request->parent_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = null;
            }

            if ($request->type === 'taluka') {
                $data['parent_id']   = $request->parent_id;
                $data['district_id'] = $request->district_id;
                $data['taluka_id']   = $request->taluka_id;
            }

            Log::info('Updating warehouse', [
                'warehouse_id' => $id,
                'data'         => $data
            ]);

            $warehouse->update($data);

            /* ======================
           Update Linked User
        ====================== */
            $user = User::where('warehouse_id', $warehouse->id)->first();

            if ($user) {
                $fullName  = trim($request->contact_person);
                $nameParts = preg_split('/\s+/', $fullName);

                $firstName = $nameParts[0];
                $lastName  = count($nameParts) > 1
                    ? implode(' ', array_slice($nameParts, 1))
                    : null;

                $user->update([
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $request->email,
                    'mobile'     => $request->contact_number,
                ]);

                Log::info('Warehouse user updated', [
                    'user_id'      => $user->id,
                    'warehouse_id' => $warehouse->id
                ]);
            }

            DB::commit();

            Log::info('Warehouse update committed', [
                'warehouse_id' => $warehouse->id
            ]);

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Warehouse update failed', [
                'warehouse_id' => $id,
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine()
            ]);

            return back()
                ->with('error', 'Something went wrong while updating warehouse.')
                ->withInput();
        }
    }



    public function destroy($id)
    {
        try {
            $warehouse = Warehouse::find($id);

            if (!$warehouse) {
                return redirect()
                    ->back()
                    ->with('error', 'Warehouse not found.');
            }

            $hasChildren = Warehouse::where('parent_id', $id)->exists();

            if ($hasChildren) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete warehouse. Child warehouses exist.');
            }

            $warehouse->delete();

            return redirect()
                ->route('warehouse.index')
                ->with('success', 'Warehouse deleted successfully.');
        } catch (\Exception $e) {

            Log::error('Warehouse Delete Error', [
                'warehouse_id' => $id,
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Something went wrong while deleting warehouse.');
        }
    }
}
