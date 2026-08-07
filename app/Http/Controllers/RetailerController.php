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
    DB::beginTransaction();

    try {

        $validated = Validator::make($request->all(), [

            'name'          => 'required|string|max:255',
            'mobile'        => 'required|digits:10|unique:users,mobile|unique:retailers,mobile',
            'email'         => 'nullable|email|unique:users,email|unique:retailers,email',

            'address'       => 'nullable|string',

            'dob'           => 'nullable|date',

            'gender'        => 'nullable|in:male,female',

            'gst_number'    => 'nullable|string|max:100',

            'shop_name'     => 'nullable|string|max:255',

            'is_active'     => 'required|boolean',

        ]);

        if ($validated->fails()) {

            return redirect()
                ->back()
                ->withErrors($validated)
                ->withInput();
        }

        $validated = $validated->validated();


        /*
        |--------------------------------------------------------------------------
        | Retailer Role
        |--------------------------------------------------------------------------
        */

        $role = Role::where('name', 'Retailer Admin')->first();

        if (!$role) {

            return back()
                ->withInput()
                ->withErrors([
                    'role' => 'Retailer Admin role not found.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Logged In DC
        |--------------------------------------------------------------------------
        */

        $loginUser = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Create User
        |--------------------------------------------------------------------------
        */

        $name = explode(' ', $validated['name']);

        $firstName = $name[0];

        $lastName = count($name) > 1
            ? implode(' ', array_slice($name, 1))
            : '';



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


        /*
        |--------------------------------------------------------------------------
        | Create Retailer
        |--------------------------------------------------------------------------
        */

        Retailer::create([

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



        DB::commit();

        return redirect()
            ->route('retailers.index')
            ->with('success', 'Retailer created successfully.');

    } catch (\Exception $e) {

        DB::rollBack();

        Log::error($e);

        return redirect()
            ->back()
            ->withInput()
            ->with('error', $e->getMessage());

    }
    }

    public function edit(Retailer $retailer)
    {
        return view('retailers.form', compact('retailer'));
    }


    public function update(Request $request, Retailer $retailer)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|unique:retailers,email,' . $retailer->id,
            'mobile'  => 'required|unique:retailers,mobile,' . $retailer->id,
            'address' => 'nullable|string',
        ]);

        $retailer->update($data);

        return redirect()->route('retailers.index')
            ->with('success', 'Retailer updated successfully');
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
