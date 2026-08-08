<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retailer;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class RetailerController extends Controller
{


    public function index()
    {
        $user = auth()->user();

        if ($user->role_id == 1 || $user->role_id == 2) {
            $retailers = Retailer::latest()->paginate(10);
        } else {
            $retailers = Retailer::where('shop_id', $user->warehouse_id)
                ->latest()
                ->paginate(10);
        }

        return view('retailers.index', compact('retailers'));
    }
   
    public function create()
    {
        $mode = 'add';
        $retailer = null;

        $user = auth()->user();

        $shops = Warehouse::where('id', $user->warehouse_id)
            ->where('type', 'distribution_center')
            ->where('status', 'active')
            ->get();

        return view('retailers.form', compact(
            'mode',
            'retailer',
            'shops'
        ));
    }

    public function store(Request $request)
    {
        Log::info('========== RETAILER STORE START ==========');

        Log::info('1. REQUEST RECEIVED', [
            'user_id' => Auth::id(),
            'data' => $request->all(),
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDATION
            |--------------------------------------------------------------------------
            */

            $validator = Validator::make($request->all(), [

                'name'       => 'required|string|max:255',

                'mobile'     => 'required|digits:10|unique:users,mobile|unique:retailers,mobile',

                'email'      => 'nullable|email|unique:users,email|unique:retailers,email',

                'address'    => 'nullable|string',

                'dob'        => 'nullable|date',

                'gender'     => 'nullable|in:male,female',

                'gst_number' => 'nullable|string|max:100',

                'shop_name'  => 'nullable|string|max:255',

                'is_active'  => 'required|boolean',

            ]);


            if ($validator->fails()) {

                Log::error('2. VALIDATION FAILED', [
                    'errors' => $validator->errors()->toArray(),
                ]);

                DB::rollBack();

                return back()
                    ->withErrors($validator)
                    ->withInput();
            }


            $validated = $validator->validated();

            Log::info('2. VALIDATION PASSED', [
                'validated' => $validated,
            ]);


            /*
            |--------------------------------------------------------------------------
            | ROLE
            |--------------------------------------------------------------------------
            */

            $role = Role::where('name', 'Retailer')->first();

            Log::info('3. ROLE CHECK', [
                'role' => $role ? $role->toArray() : null,
            ]);


            if (!$role) {

                Log::error('3. RETAILER ADMIN ROLE NOT FOUND');

                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', 'Retailer Admin role not found.');
            }


            /*
            |--------------------------------------------------------------------------
            | LOGIN USER
            |--------------------------------------------------------------------------
            */

            $loginUser = Auth::user();

            Log::info('4. LOGIN USER', [

                'user_id' => $loginUser->id ?? null,

                'warehouse_id' => $loginUser->warehouse_id ?? null,

                'role_id' => $loginUser->role_id ?? null,

            ]);


            if (!$loginUser) {

                throw new \Exception('Logged in user not found.');
            }


            if (!$loginUser->warehouse_id) {

                throw new \Exception('Logged in user warehouse_id is NULL.');
            }


            /*
            |--------------------------------------------------------------------------
            | NAME
            |--------------------------------------------------------------------------
            */

            $name = explode(' ', trim($validated['name']));

            $firstName = $name[0];

            $lastName = count($name) > 1
                ? implode(' ', array_slice($name, 1))
                : '';


            /*
            |--------------------------------------------------------------------------
            | CREATE USER
            |--------------------------------------------------------------------------
            */

            Log::info('5. CREATING USER', [

                'warehouse_id' => $loginUser->warehouse_id,

                'first_name' => $firstName,

                'last_name' => $lastName,

                'email' => $validated['email'] ?? null,

                'mobile' => $validated['mobile'],

                'role_id' => $role->id,

            ]);


            $user = User::create([

                'warehouse_id' => $loginUser->warehouse_id,

                'first_name'   => $firstName,

                'last_name'    => $lastName,

                'email'        => $validated['email'] ?? null,

                'mobile'       => $validated['mobile'],

                'password'     => Hash::make('pass@123'),

                'role_id'      => $role->id,

                'status'       => 1,

            ]);


            Log::info('6. USER CREATED', [

                'user_id' => $user->id,

                'warehouse_id' => $user->warehouse_id,

            ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE RETAILER
            |--------------------------------------------------------------------------
            */

            Log::info('7. CREATING RETAILER', [

                'user_id' => $user->id,

                'shop_id' => $loginUser->warehouse_id,

                'name' => $validated['name'],

                'mobile' => $validated['mobile'],

            ]);


            $retailer = Retailer::create([

                'user_id'       => $user->id,

                'shop_id'       => $loginUser->warehouse_id,

                'name'          => $validated['name'],

                'email'         => $validated['email'] ?? null,

                'mobile'        => $validated['mobile'],

                'address'       => $validated['address'] ?? null,

                'dob'           => $validated['dob'] ?? null,

                'gender'        => $validated['gender'] ?? null,

                'gst_number'    => $validated['gst_number'] ?? null,

                'shop_name'     => $validated['shop_name'] ?? null,

                'is_active'     => $validated['is_active'],

                'created_by'    => Auth::id(),

            ]);


            Log::info('8. RETAILER CREATED', [

                'retailer_id' => $retailer->id,

                'user_id' => $retailer->user_id,

                'shop_id' => $retailer->shop_id,

            ]);


            /*
            |--------------------------------------------------------------------------
            | COMMIT
            |--------------------------------------------------------------------------
            */

            DB::commit();

            Log::info('9. TRANSACTION COMMITTED');

            Log::info('========== RETAILER STORE SUCCESS ==========');


            return redirect()
                ->route('retailers.index')
                ->with('success', 'Retailer created successfully.');


        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('========== RETAILER STORE FAILED ==========');

            Log::error('ERROR MESSAGE', [
                'message' => $e->getMessage(),
            ]);

            Log::error('ERROR FILE', [
                'file' => $e->getFile(),
            ]);

            Log::error('ERROR LINE', [
                'line' => $e->getLine(),
            ]);

            Log::error('ERROR CODE', [
                'code' => $e->getCode(),
            ]);

            Log::error('REQUEST DATA', [
                'data' => $request->all(),
            ]);

            Log::error('STACK TRACE', [
                'trace' => $e->getTraceAsString(),
            ]);


            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
   
    /**
     * Edit Retailer
     */
    public function edit(Retailer $retailer)
    {
        /*
        |--------------------------------------------------------------------------
        | Get Distribution Centers / Shops
        |--------------------------------------------------------------------------
        */

        $shops = Warehouse::where('type', 'distribution_center')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Return Edit Form
        |--------------------------------------------------------------------------
        */

        return view(
            'retailers.form',
            compact(
                'retailer',
                'shops'
            )
        );
    }

    /**
     * Update Retailer
     */
    public function update(Request $request, Retailer $retailer)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate Request
        |--------------------------------------------------------------------------
        */

        $data = $request->validate([

            'shop_id' => [
                'required',
                'exists:warehouses,id'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'nullable',
                'email',
                'unique:retailers,email,' . $retailer->id
            ],

            'mobile' => [
                'required',
                'string',
                'unique:retailers,mobile,' . $retailer->id
            ],

            'address' => [
                'nullable',
                'string'
            ],

            'dob' => [
                'nullable',
                'date'
            ],

            'gender' => [
                'nullable',
                'in:male,female,other'
            ],

            'gst_number' => [
                'nullable',
                'string',
                'max:255'
            ],

            'shop_name' => [
                'nullable',
                'string',
                'max:255'
            ],

            'state_id' => [
                'nullable',
                'exists:states,id'
            ],

            'district_id' => [
                'nullable',
                'exists:districts,id'
            ],

            'taluka_id' => [
                'nullable',
                'exists:talukas,id'
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Log Update Request
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Update - Request',
            [
                'retailer_id' => $retailer->id,
                'user_id' => auth()->id(),
                'data' => $data,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 3. Update Retailer
        |--------------------------------------------------------------------------
        */

        $retailer->update($data);


        /*
        |--------------------------------------------------------------------------
        | 4. Success Log
        |--------------------------------------------------------------------------
        */

        \Log::info(
            'Retailer Update - Successfully Updated',
            [
                'retailer_id' => $retailer->id,
                'user_id' => auth()->id(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | 5. Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('retailers.index')
            ->with(
                'success',
                'Retailer updated successfully.'
            );
    }

    public function destroy(Retailer $retailer)
    {
        $retailer->delete();

        return back()->with('success', 'Retailer deleted');
    }

    public function toggleStatus(Retailer $retailer)
    {
        $retailer->update([
            'is_active' => !$retailer->is_active
        ]);

        return back()->with('success', 'Status updated');
    }


}
