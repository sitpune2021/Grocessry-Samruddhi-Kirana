<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use App\Models\DeliveryAgent;
use App\Models\GroceryShop;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Warehouse;

use Illuminate\Validation\ValidationException;

class DeliveryAgentController extends Controller
{


    public function index()
    {
        $agents = DeliveryAgent::with(['user', 'shop'])
            ->latest()
            ->paginate(10);

        return view('menus.delivery-agent.delivery-agent.index', compact('agents'));
    }

    public function create()
    {
        $mode = 'add';
        $agent = null;
        $shops = Warehouse::where('status', 'active')
            ->where('type', 'distribution_center')->get();
        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('mode', 'agent', 'shops'));
    }

    public function store(Request $request)
    {

        Log::info('Delivery Agent store process started', [
            'requested_by' => Auth::id(),
            'ip' => $request->ip()
        ]);

        DB::beginTransaction();

        try {

            Log::info('Validating delivery agent request');

            $validated = Validator::make($request->all(), [
                'name'            => 'required|string|max:255',
                'last_name'       => 'required|string|max:255',
                'mobile'          => 'required|digits:10|unique:users,mobile',
                'email'           => 'nullable|email|unique:users,email',
                'password'        => 'nullable|min:6',
                //'warehouse_id' => 'required|exists:warehouses,id',
                'shop_id' => 'required|exists:warehouses,id',
                // 'shop_id'         => 'required|exists:grocery_shops,id',
                'dob'             => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'gender'          => 'nullable|in:male,female',
                'address'         => 'nullable|string',
                'active_status'   => 'required|boolean',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'aadhaar_card'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'driving_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            if ($validated->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validated)
                    ->withInput();
            }

            /* THIS LINE FIXES EVERYTHING */
            $validated = $validated->validated();

            Log::info('Fetching Delivery Agent role');

            //$role = Role::where('name', 'Delivery Agent')->firstOrFail();
            $role = Role::where('name', 'Delivery Agent')->first();

            if (!$role) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'role' => 'Delivery Agent role is not configured. Please contact admin.'
                    ]);
            }

            Log::info('Role found', [
                'role_id' => $role->id
            ]);


            Log::info('Creating user record');
            $profileImage = null;

            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $profileImage = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('profile_photos', $profileImage, 'public');
            }


            $user = User::create([
                'first_name'      => $validated['name'],
                'last_name'      => $validated['last_name'],
                'email'     => $validated['email'] ?? null,
                'mobile'    => $validated['mobile'],
                'password'  => Hash::make('pass@123'),
                'role_id'   => $role->id,
                'profile_photo'   => $profileImage,
                //'warehouse_id' => $validated['warehouse_id'],
                'shop_id'       => $validated['shop_id'],
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id
            ]);


            Log::info('Uploading delivery agent files');


            $aadhaarPath  = null;
            $licensePath  = null;

            if ($request->hasFile('aadhaar_card')) {

                $file = $request->file('aadhaar_card');
                $aadhaarPath = $file->getClientOriginalName();
                $file->storeAs('delivery_agents/aadhaar', $aadhaarPath, 'public');

                Log::info('Aadhaar uploaded', [
                    'path' => $aadhaarPath
                ]);
            }

            if ($request->hasFile('driving_license')) {

                $file = $request->file('driving_license');
                $licensePath = $file->getClientOriginalName();

                $file->storeAs('delivery_agents/license', $licensePath, 'public');
                Log::info('Driving license uploaded', [
                    'path' => $licensePath
                ]);
            }

            Log::info('Creating delivery agent record');

            DeliveryAgent::create([
                'user_id'         => $user->id,
                //'shop_id'         => $validated['shop_id'] ?? null,
                //'warehouse_id'  => $validated['warehouse_id'],
                'shop_id'   => $validated['shop_id'],
                'dob'             => $validated['dob'] ?? null,
                'gender'          => $validated['gender'] ?? null,
                'address'         => $validated['address'] ?? null,

                'aadhaar_card'    => $aadhaarPath,
                'driving_license' => $licensePath,
                'active_status'   => $validated['active_status'],
                'created_by'      => Auth::id(),
            ]);

            Log::info('Delivery agent created successfully', [
                'user_id' => $user->id,
                'shop_id' => $validated['shop_id'] ?? null
            ]);

            DB::commit();

            Log::info('Transaction committed successfully');

            return redirect()
                ->route('delivery-agents.index')
                ->with('success', 'Delivery Agent created successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Error while creating delivery agent', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->except(['password'])
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function show(string $id)
    {

        $agent = DeliveryAgent::with('user')->findOrFail($id);
        $shops =  Warehouse::where('status', 'active')
            ->where('type', 'distribution_center')->get();
        $mode = 'view';
        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode', 'shops'));
    }

    public function edit(string $id)
    {
        $agent = DeliveryAgent::with('user')->findOrFail($id);
        $shops =  Warehouse::where('status', 'active')
            ->where('type', 'distribution_center')->get();
        $mode = 'edit';

        return view('menus.delivery-agent.delivery-agent.add-delivery-agent', compact('agent', 'mode', 'shops'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            Log::info('===== Delivery Agent Update START =====', [
                'agent_id' => $id,
                'request' => $request->all()
            ]);

            $agent = DeliveryAgent::with('user')->findOrFail($id);

            Log::info('Agent fetched', [
                'agent' => $agent->toArray()
            ]);

            /* ---------------- Validation ---------------- */
            $validated = $request->validate([
                'shop_id' => 'required|exists:warehouses,id',
                'name'          => 'required|string|max:255',
                'last_name'     => 'required|string|max:255',
                'dob'           => 'nullable|date|before_or_equal:' . now()->subYears(18)->format('Y-m-d'),
                'gender'        => 'nullable|in:male,female',
                'address'       => 'nullable|string',
                'active_status' => 'required|boolean',

                'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'aadhaar_card'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'driving_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            Log::info('Validation success', $validated);

            /* ---------------- USER ---------------- */
            $user = $agent->user;

            if (!$user) {
                Log::error('User not found for agent', ['agent_id' => $id]);
                throw new \Exception('User not linked');
            }

            Log::info('User found', ['user_id' => $user->id]);

            /* ---------------- PROFILE IMAGE ---------------- */
            $profilePhotoName = $user->profile_photo;

            if ($request->hasFile('profile_image')) {

                Log::info('Uploading profile image...');

                if ($profilePhotoName) {
                    Storage::disk('public')->delete('profile_photos/' . $profilePhotoName);
                    Log::info('Old profile image deleted', ['old' => $profilePhotoName]);
                }

                $file = $request->file('profile_image');
                $profilePhotoName = time() . '_' . $file->getClientOriginalName();

                $file->storeAs('profile_photos', $profilePhotoName, 'public');

                Log::info('New profile image stored', ['file' => $profilePhotoName]);
            }

            /* ---------------- UPDATE USER ---------------- */
            $user->update([
                'first_name'    => $validated['name'],
                'last_name'     => $validated['last_name'],
                'mobile'        => $request->mobile,
                'email'         => $request->email ?? null,
                'profile_photo' => $profilePhotoName,
            ]);

            Log::info('User updated');

            /* ---------------- FILES ---------------- */

            // Aadhaar
            if ($request->hasFile('aadhaar_card')) {

                Log::info('Uploading Aadhaar...');

                if ($agent->aadhaar_card) {
                    Storage::disk('public')->delete('delivery_agents/aadhaar/' . $agent->aadhaar_card);
                }

                $file = $request->file('aadhaar_card');
                $aadhaarName = time() . '_aadhaar_' . $file->getClientOriginalName();

                $file->storeAs('delivery_agents/aadhaar/', $aadhaarName, 'public');

                $agent->aadhaar_card = $aadhaarName;

                Log::info('Aadhaar uploaded', ['file' => $aadhaarName]);
            }

            // License
            if ($request->hasFile('driving_license')) {

                Log::info('Uploading License...');

                if ($agent->driving_license) {
                    Storage::disk('public')->delete('delivery_agents/license/' . $agent->driving_license);
                }

                $file = $request->file('driving_license');
                $licenseName = time() . '_license_' . $file->getClientOriginalName();

                $file->storeAs('delivery_agents/license/', $licenseName, 'public');

                $agent->driving_license = $licenseName;

                Log::info('License uploaded', ['file' => $licenseName]);
            }

            /* ---------------- UPDATE AGENT ---------------- */
            $data = [
                'shop_id'       => $validated['shop_id'],
                'dob'           => $validated['dob'] ?? null,
                'gender'        => $validated['gender'] ?? null,
                'address'       => $validated['address'] ?? null,
                'active_status' => $validated['active_status'],
                'updated_by'    => Auth::id(),
            ];

            if (isset($aadhaarName)) {
                $data['aadhaar_card'] = $aadhaarName;
            }

            if (isset($licenseName)) {
                $data['driving_license'] = $licenseName;
            }

            Log::info('Updating agent with data', $data);

            $agent->update($data);

            DB::commit();

            Log::info('===== Delivery Agent Update SUCCESS =====');

            return redirect()
                ->route('delivery-agents.index')
                ->with('success', 'Delivery Agent updated successfully');
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('===== Delivery Agent Update FAILED =====', [
                'agent_id' => $id,
                'message'  => $e->getMessage(),
                'line'     => $e->getLine(),
                'file'     => $e->getFile(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong');
        }
    }
    public function destroy(string $id)
    {
        try {
            $agent = DeliveryAgent::findOrFail($id);
            $agent->delete();

            Log::info('Delivery Agent deleted', [
                'agent_id' => $id,
                'user_id'  => Auth::id(),
            ]);

            return back()->with('success', 'Delivery Agent deleted');
        } catch (\Exception $e) {
            Log::error('Delivery Agent delete failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to delete');
        }
    }

    public function assignDelivery(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id', // other customer_orders table
                'delivery_agent_id' => 'required|exists:delivery_agents,id',
            ]);

            DB::beginTransaction();

            $order = \App\Models\Order::lockForUpdate()->findOrFail($validated['order_id']);

            if ($order->delivery_agent_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'order_id' => 'Delivery agent already assigned to this order.'
                ]);
            }

            // ✅ Assign agent
            $order->delivery_agent_id = $validated['delivery_agent_id'];
            $order->status = 'assigned';
            $order->save();

            // deliveries table
            $delivery = DB::table('deliveries')
                ->where('order_id', $order->id)
                ->first();

            if ($delivery) {
                // UPDATE
                DB::table('deliveries')
                    ->where('order_id', $order->id)
                    ->update([
                        'delivery_agent_id' => $validated['delivery_agent_id'],
                        'status' => 'assigned',
                        'updated_at' => now(),
                    ]);
            } else {
                // INSERT
                DB::table('deliveries')->insert([
                    'order_id' => $order->id,
                    'delivery_agent_id' => $validated['delivery_agent_id'],
                    'status' => 'assigned',
                    'customer_otp' => rand(1000, 9999),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            DB::commit();

            return back()->with('success', 'Delivery agent assigned successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Something went wrong. Please try again.');
        }
    }

    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required|in:delivered,rejected',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::lockForUpdate()->findOrFail($request->order_id);

            if ($order->status !== 'assigned') {
                return back()->with('error', 'Order is not assigned');
            }

            /* =====================
           DELIVERED
        ===================== */
            if ($request->status === 'delivered') {

                // orders table
                $order->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                    'payment_status' => 'paid',
                ]);

                // deliveries table
                DB::table('deliveries')
                    ->where('order_id', $order->id)
                    ->update([
                        'status' => 'delivered',
                        'updated_at' => now(),
                    ]);
            }

            /* =====================
           REJECTED
        ===================== */
            if ($request->status === 'rejected') {

                // orders table
                $order->update([
                    'status' => 'rejected',
                    'cancelled_at' => now(),
                    'cancel_reason' => 'Rejected by admin',
                ]);

                // deliveries table
                DB::table('deliveries')
                    ->where('order_id', $order->id)
                    ->update([
                        'status' => 'rejected',
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();
            return back()->with('success', 'Order status updated successfully');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong');
        }
    }

    
}
