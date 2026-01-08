<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
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

    public function profile()
    {
        $users = User::with('role')->paginate(10);

        return view('userProfile.index', compact('users'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function Store(Request $request)
    {
        // Log: Raw request
        Log::info('User Store Request Received', ['request' => $request->all()]);
        Log::info('RAW BODY', ['body' => $request->getContent()]);

        try {

            // Log: Starting validation
            Log::info('User Store Validation Started');

            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name'  => 'required|string|max:100',
                'email'      => 'nullable|email|unique:users,email',
                'mobile'     => 'required|digits:10|unique:users,mobile',
                'role'       => 'required|in:admin,user,manager,staff',
                'password'   => 'required|min:8|confirmed',
            ]);

            Log::info('User Store Validation Passed');

            // Create User
            $admin = User::create([
                'first_name' => $request->first_name,
                'email'      => $request->email,
                'last_name'  => $request->last_name,
                'mobile'     => $request->mobile,
                'role'       => $request->role,
                'password'   => Hash::make($request->password),
            ]);

            // Log: After creating user
            Log::info('User Created Successfully', [
                'user_id' => $admin->id,
                'email'   => $admin->email
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'User created successfully',
                'data'    => $admin
            ], 200);
        } catch (\Exception $e) {

            // Log: Error
            Log::error('User Store Error', [
                'error_message' => $e->getMessage(),
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
        
    }



    public function show(string $id)
    {
        Log::info('User Show Request Received', ['user_id' => $id]);

        try {

            $user = User::find($id);

            if (!$user) {
                Log::warning('User Not Found', ['user_id' => $id]);

                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            Log::info('User Found Successfully', ['user_id' => $id]);

            return response()->json([
                'status'  => true,
                'message' => 'User fetched successfully',
                'data'    => $user
            ], 200);
        } catch (\Exception $e) {

            Log::error('User Show Error', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
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
        Log::info('User Update Request Received', [
            'user_id' => $id,
            'request' => $request->all()
        ]);

        try {

            $user = User::find($id);

            if (!$user) {
                Log::warning("User Not Found", ['user_id' => $id]);

                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            Log::info("User Found", ['user_id' => $user->id]);

            // Validation
            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name'  => 'required|string|max:100',
                'email'      => 'nullable|email|unique:users,email,' . $id,
                'mobile'     => 'required|digits:10|unique:users,mobile,' . $id,
                'role'       => 'required|in:admin,user,manager,staff',
                'password'   => 'nullable|min:8|confirmed',
            ]);

            Log::info("User Update Validation Passed");

            // Update values
            $user->first_name = $request->first_name;
            $user->last_name  = $request->last_name;
            $user->email      = $request->email;
            $user->mobile     = $request->mobile;
            $user->role       = $request->role;

            if ($request->password) {
                $user->password = Hash::make($request->password);
                Log::info("Password Updated for User", ['user_id' => $user->id]);
            }

            $user->save();

            Log::info("User Updated Successfully", ['user_id' => $user->id]);

            return response()->json([
                'status'  => true,
                'message' => 'User updated successfully',
                'data'    => $user
            ], 200);
        } catch (\Exception $e) {

            Log::error("User Update Error", [
                'user_id' => $id,
                'error'   => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Log::info('User Delete Request Received', ['user_id' => $id]);

        try {

            $user = User::find($id);

            if (!$user) {
                Log::warning('User Not Found for Delete', ['user_id' => $id]);

                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Soft Delete
            $user->delete();

            Log::info('User Soft Deleted Successfully', ['user_id' => $id]);

            return response()->json([
                'status'  => true,
                'message' => 'User deleted successfully (soft delete)'
            ], 200);
        } catch (\Exception $e) {

            Log::error('User Delete Error', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        // Always get Eloquent model
        $user = User::findOrFail(Auth::id());

        // Validation (no confirm password)
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'mobile'         => 'nullable|string|max:20',
            'profile_photo'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'new_password'   => 'nullable|min:6',
            'password'       => 'nullable', // current password
        ]);

        /**
         * PASSWORD UPDATE
         */
        if ($request->filled('new_password')) {

            if (!$request->filled('password')) {
                return back()->withErrors([
                    'password' => 'Current password is required'
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'Current password is incorrect'
                ]);
            }

            $user->password = Hash::make($request->new_password);
        }

        /**
         * PROFILE UPDATE
         */
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->mobile     = $request->mobile;

        /**
         * PHOTO
         */
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profiles', 'public');
            $user->profile_photo = $path;
        }

        // NOW THIS WILL WORK
        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }
}
