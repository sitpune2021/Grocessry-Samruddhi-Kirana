<?php

namespace App\Http\Controllers;

use App\Models\DeliveryAgent;
use App\Models\DriverVehicle;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class VehicleAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch delivery agent vehicles with driver info, paginate 10 per page
        $driverVehicles = DriverVehicle::with('driver')
            ->latest()
            ->paginate(10);

        return view('menus.delivery-agent.vehicle-assignment.index', compact('driverVehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mode = 'add';

        $deliveryAgentRoleId = Role::where('name', 'Delivery Agent')->value('id');
        $agents = User::where('role_id', $deliveryAgentRoleId)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();
        return view('menus.delivery-agent.vehicle-assignment.add-delivery-agent', compact('mode', 'agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log incoming request
            Log::info('DriverVehicle Store Request', [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            // Validate input
            $validated = $request->validate([
                'driver_id'     => 'required|exists:users,id',
                'vehicle_no'    => 'required|string|max:255|unique:driver_vehicles,vehicle_no',
                'vehicle_type'  => 'nullable|string|max:255',
                'license_no'    => 'nullable|string|max:255|unique:driver_vehicles,license_no',
                'active_status' => 'required|boolean',
            ]);

            // Create record
            $driverVehicle = DriverVehicle::create([
                'driver_id'    => $validated['driver_id'],
                'vehicle_no'   => $validated['vehicle_no'],
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'license_no'   => $validated['license_no'] ?? null,
                'active'       => $validated['active_status'], // store active status
            ]);

            // Log success
            Log::info('DriverVehicle Created Successfully', [
                'driver_vehicle_id' => $driverVehicle->id,
                'driver_id' => $driverVehicle->driver_id,
                'active' => $driverVehicle->active
            ]);

            return redirect()
                ->route('vehicle-assignments.index')
                ->with('success', 'Agent created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('DriverVehicle Validation Failed', [
                'errors' => $e->errors()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('DriverVehicle Store Failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            Log::info('DriverVehicle Show Request', [
                'driver_vehicle_id' => $id,
                'user_id' => Auth::id()
            ]);

            $driverVehicle = DriverVehicle::with('driver')->findOrFail($id);

            $agents = User::orderBy('first_name')->get();


            return view('menus.delivery-agent.vehicle-assignment.add-delivery-agent', compact('driverVehicle', 'agents'))
                ->with('mode', 'view');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('DriverVehicle Not Found', ['id' => $id]);
            return redirect()->route('vehicle-assignments.index')->with('error', 'Record not found');
        } catch (\Exception $e) {
            Log::error('DriverVehicle Show Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);
            return redirect()->route('vehicle-assignments.index')->with('error', 'Something went wrong');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $mode = 'edit';
            $driverVehicle = DriverVehicle::findOrFail($id);
            $agents = User::orderBy('first_name')->get();


            return view('menus.delivery-agent.vehicle-assignment.add-delivery-agent', compact('driverVehicle', 'agents', 'mode'));
        } catch (\Exception $e) {
            Log::error('DriverVehicle Edit Error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return redirect()->route('vehicle-assignments.index')->with('error', 'Record not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $driverVehicle = DriverVehicle::findOrFail($id);

            Log::info('DriverVehicle Update Request', [
                'id' => $id,
                'data' => $request->all()
            ]);

            $validated = $request->validate([
                'driver_id'     => 'required|exists:users,id',
                'vehicle_no' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('driver_vehicles', 'vehicle_no')->ignore($id),
                ],

                'vehicle_type' => 'nullable|string|max:255',

                'license_no' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('driver_vehicles', 'license_no')->ignore($id),
                ],

                'active_status' => 'required|in:0,1',
            ]);

            $driverVehicle->update([
                'driver_id'    => $validated['driver_id'],
                'vehicle_no'   => $validated['vehicle_no'],
                'vehicle_type' => $validated['vehicle_type'],
                'license_no'   => $validated['license_no'],
                'active'       => $validated['active_status'],
            ]);

            Log::info('DriverVehicle Updated', ['id' => $id]);

            return redirect()->route('vehicle-assignments.index')->with('success', 'Agent updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('DriverVehicle Update Error', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Update failed');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $driverVehicle = DriverVehicle::findOrFail($id);
            $driverVehicle->delete();

            Log::info('DriverVehicle Deleted', ['id' => $id]);

            return redirect()->route('vehicle-assignments.index')->with('success', 'Agent deleted successfully');
        } catch (\Exception $e) {
            Log::error('DriverVehicle Delete Error', ['message' => $e->getMessage()]);
            return redirect()->route('vehicle-assignments.index')->with('error', 'Delete failed');
        }
    }
}
