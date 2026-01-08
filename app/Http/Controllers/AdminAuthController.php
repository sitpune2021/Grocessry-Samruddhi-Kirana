<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminAuthController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('id', 'desc')->paginate(10);
        $warehouses = Warehouse::all();
        return view('userProfile.index', compact('users', 'warehouses'));
    }

    public function createUser()
    {
        $mode = 'add';
        $roles = Role::all(); // fetch all roles
        $user = new User(); // ✅ empty model
        $warehouses = Warehouse::all();

        return view('userProfile.add-user', compact('mode', 'user', 'roles', 'warehouses'));
    }
    public function store(Request $request)
    {

        // Log the incoming request (excluding sensitive data)
        Log::info('🔹 User Store Request Received', [
            'payload' => $request->except(['password', 'password_confirmation'])
        ]);

        // Validation start
        Log::info('🔹 User Store Validation Started');
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'nullable|email|unique:users,email',
            'mobile'         => 'required|digits:10|unique:users,mobile',
            'role_id'        => 'required|exists:roles,id',
            'status'         => 'required|boolean',
            'profile_photo'  => 'nullable|image',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);
        try {

            Log::info('✅ User Store Validation Passed');

            // Profile photo upload
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $photoPath = $request->file('profile_photo')->store('profile_photos', 'public');
                Log::info('✅ Profile photo uploaded', ['path' => $photoPath]);
            }

            // Create user
            $user = User::create([
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'email'         => $request->email,
                'mobile'        => $request->mobile,
                'warehouse_id'  => $request->warehouse_id,
                'role_id'       => $request->role_id,
                'status'        => $request->status,
                'profile_photo' => $photoPath,
                'password'      => Hash::make('pass@123'),
            ]);
            if ($request->warehouse_id) {

                $warehouse = Warehouse::find($request->warehouse_id);

                if ($warehouse) {
                    $warehouse->update([
                        'contact_person' => $user->first_name . ' ' . $user->last_name,
                        'contact_number' => $user->mobile,
                        'email'          => $user->email,
                    ]);
                }
            }

            Log::info('✅ User created successfully', ['user_id' => $user->id]);

            return redirect()->route('user.profile')->with('success', 'User created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            Log::warning('⚠️ User Store Validation Failed', ['errors' => $e->errors()]);
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('❌ User Store Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile()
            ]);
            return back()->withInput()->with('error', 'Something went wrong while creating user');
        }
    }

    public function show($id)
    {
        $mode = 'view';
        $warehouses = Warehouse::all();
        $user = User::with('role')->findOrFail($id);
        $roles = Role::all();

        return view('userProfile.add-user', compact('mode', 'user', 'roles', 'warehouses'));
    }
    public function editUser($id)
    {
        $mode = 'edit';
        $user = User::findOrFail($id);
        $warehouses = Warehouse::all();
        $roles = Role::all();
        return view('userProfile.add-user', compact('mode', 'user', 'roles', 'warehouses'));
    }

    public function update(Request $request, $id)
    {
        Log::info('🔹 User Update Request Received', [
            'user_id' => $id,
            'payload' => $request->except(['profile_photo'])
        ]);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'email'         => 'nullable|email|unique:users,email,' . $user->id,
            'mobile'        => 'required|digits:10|unique:users,mobile,' . $user->id,
            'role_id'       => 'required|exists:roles,id', // ✅ FIXED
            'status'        => 'required|boolean',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        Log::info('✅ User Update Validation Passed', $validated);

        // 📸 Replace profile photo
        if ($request->hasFile('profile_photo')) {
            Log::info('📸 Profile Photo Update Started', ['user_id' => $id]);

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
                Log::info('🗑 Old Profile Photo Deleted');
            }

            $user->profile_photo = $request->file('profile_photo')
                ->store('profile_photos', 'public');

            Log::info('📸 New Profile Photo Stored', [
                'path' => $user->profile_photo
            ]);
        }

        // 🛠 Update user
        $user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'mobile'     => $request->mobile,
            'role_id'    => $request->role_id,
            'warehouse_id'  => $request->warehouse_id,
            'status'     => $request->status,
        ]);

        Log::info('✅ User Updated Successfully', [
            'user_id' => $user->id,
            'role_id' => $request->role_id
        ]);

        return redirect()
            ->route('user.profile')
            ->with('success', 'User updated successfully');
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()
            ->route('user.profile')
            ->with('success', 'User deleted successfully');
    }


    public function loginForm()
    {
        return view('admin-login.auth-login');
    }


    public function login(Request $request)
    {
<<<<<<< HEAD
=======

>>>>>>> 08dfd4843088d84033340d0782a98eb1ceff8bb6
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()
                ->withErrors(['email' => 'Invalid email or password'])
                ->withInput();
        }

        $request->session()->regenerate();
<<<<<<< HEAD
=======

>>>>>>> 08dfd4843088d84033340d0782a98eb1ceff8bb6
        return redirect()->route('dashboard')
            ->with('success', 'Successfully logged in!');
    }

    public function logout(Request $request)
    {
        try {
            $user = Auth::user();

            Log::info('🔐 Logout initiated', [
                'user_id' => $user->id ?? null,
                'email'   => $user->email ?? null,
                'ip'      => $request->ip(),
                'time'    => now()->toDateTimeString(),
            ]);

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::info('✅ User logged out successfully', [
                'user_id' => $user->id ?? null,
                'ip'      => $request->ip(),
                'time'    => now()->toDateTimeString(),
            ]);

            return redirect()->route('login.form')
                ->with('success', 'Logged out successfully!');
        } catch (\Exception $e) {

            Log::error('❌ Logout failed', [
                'error_message' => $e->getMessage(),
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
                'ip'            => $request->ip(),
            ]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email not found');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Redirect to login page with success message
        return redirect()->route('login.form')
            ->with('success', 'Password reset successfully. Please login.');
    }
}
