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
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    
    
    public function profile()
    {
        $users = User::with('role')->paginate(20);

        return view('userProfile.index', compact('users'));
    }

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
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'mobile'         => 'required',
            'password'       => 'required_with:new_password',
            'new_password'   => 'nullable|string|min:6',
            'profile_photo'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = User::findOrFail(Auth::id());

        // -----------------------
        // BASIC PROFILE UPDATE
        // -----------------------
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->mobile     = $request->mobile;

        // -----------------------
        // PASSWORD UPDATE
        // -----------------------
        if ($request->filled('new_password')) {
            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'Current password is incorrect'
                ]);
            }
            $user->password = Hash::make($request->new_password);
        }

        // -----------------------
        // PROFILE PHOTO UPLOAD
        // -----------------------
        if ($request->hasFile('profile_photo')) {

            // Delete old photo
            if ($user->profile_photo && Storage::exists('public/' . $user->profile_photo)) {
                Storage::delete('public/' . $user->profile_photo);
            }

            // ORIGINAL FILE NAME (NO RENAME)
            $originalName = $request->file('profile_photo')->getClientOriginalName();

            // Store file
            $path = $request->file('profile_photo')
                ->storeAs('public/profiles', $originalName);

            // Save path in DB
            $user->profile_photo = 'profiles/' . $originalName;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully');
    }


}
