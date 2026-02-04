<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerProductReturn;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryPartnerReturnController extends Controller
{
    public function startReturn(Request $request, $returnId)
    {
        $user = $request->user(); // delivery agent

        // ✅ Validate role
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Validate input
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // ✅ Fetch return
        $return = CustomerProductReturn::where('id', $returnId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ], 404);
        }

        // ✅ Prevent duplicate start
        if ($return->status === 'RETURNING_TO_STORE') {
            return response()->json([
                'status' => false,
                'message' => 'Return already in progress'
            ], 409);
        }

        // ✅ Optional: strict status check (recommended)
        if ($return->status !== 'PICKED_FOR_RETURN') {
            return response()->json([
                'status' => false,
                'message' => 'Invalid return state'
            ], 422);
        }

        // ✅ Update return
        $return->update([
            'status'            => 'RETURNING_TO_STORE',
            'return_started_at' => now(),
            'start_latitude'    => $request->latitude,
            'start_longitude'   => $request->longitude,
        ]);

        // ✅ Mark agent online
        if ($user->is_online == 0) {
            $user->update(['is_online' => 1]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Return journey started',
            'data' => [
                'return_id'  => $return->id,
                'status'     => $return->status,
                'started_at' => $return->return_started_at
            ]
        ]);
    }

    public function arriveAtStore(Request $request, $returnId)
    {
        $user = $request->user(); // delivery agent

        // ✅ Role check
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Validate input
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // ✅ Fetch return
        $return = CustomerProductReturn::where('id', $returnId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ], 404);
        }

        // ✅ Status must be RETURNING_TO_STORE
        if ($return->status !== 'RETURNING_TO_STORE') {
            return response()->json([
                'status' => false,
                'message' => 'Return not in transit'
            ], 422);
        }

        // ✅ Update arrival
        $return->update([
            'status'        => 'ARRIVED_AT_STORE',
            'returned_at'   => now(), // arrival time
            'start_latitude'  => $request->latitude,
            'start_longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Arrived at store successfully',
            'data' => [
                'return_id' => $return->id,
                'status' => $return->status,
                'arrived_at' => $return->returned_at
            ]
        ]);
    }

    public function printReceipt(Request $request, $returnId)
    {
        $user = $request->user(); // delivery agent

        // ✅ Role check
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Fetch return
        $return = CustomerProductReturn::with([
            'product',
            'customer',
            'warehouse'
        ])
            ->where('id', $returnId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ], 404);
        }

        // ✅ Only after successful handover
        if ($return->status !== 'RETURNED') {
            return response()->json([
                'status' => false,
                'message' => 'Receipt available only after return completion'
            ], 422);
        }

        // ✅ Generate PDF
        $pdf = Pdf::loadView('returns.receipt', [
            'return' => $return,
            'agent'  => $user
        ]);

        return $pdf->download('product-returns.return_receipt_' . $return->id . '.pdf');
    }

    public function confirmHandover(Request $request, $returnId)
    {
        $user = $request->user(); // delivery agent

        // ✅ Role check
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Validate input
        $request->validate([
            'manager_signature' => 'required|string',
            'agent_signature'   => 'required|string',
        ]);

        // ✅ Fetch return
        $return = CustomerProductReturn::where('id', $returnId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ], 404);
        }

        // ✅ Status must be ARRIVED_AT_STORE
        if ($return->status !== 'ARRIVED_AT_STORE') {
            return response()->json([
                'status' => false,
                'message' => 'Return not ready for handover'
            ], 422);
        }

        // ✅ Save handover confirmation
        $return->update([
            'manager_signature'       => $request->manager_signature,
            'agent_signature'         => $request->agent_signature,
            'handover_confirmed_at'   => now(),
            'status'                  => 'RETURNED',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Handover confirmed successfully',
            'data' => [
                'return_id' => $return->id,
                'status' => $return->status,
                'handover_time' => $return->handover_confirmed_at
            ]
        ]);
    }

    public function getHandoverDetails(Request $request, $returnId)
    {
        $user = $request->user(); // delivery agent

        // ✅ Role check
        if (!$user->role || strtolower($user->role->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // ✅ Fetch return with relations
        $return = CustomerProductReturn::with([
            'product:id,name',
            'customer:id,first_name,last_name',
            'warehouse:id,name'
        ])
            ->where('id', $returnId)
            ->where('delivery_agent_id', $user->id)
            ->first();

        if (!$return) {
            return response()->json([
                'status' => false,
                'message' => 'Return not found'
            ], 404);
        }

        // ✅ Build QR data (can be scanned by warehouse)
        $qrData = [
            'return_id'   => $return->id,
            'order_id'    => $return->order_id,
            'product_id'  => $return->product_id,
            'quantity'    => $return->quantity,
            'warehouse'   => $return->warehouse_id,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Handover details fetched successfully',
            'data' => [
                'return_id' => $return->id,
                'status'    => $return->status,

                // Item details
                'product' => [
                    'id'   => $return->product->id ?? null,
                    'name' => $return->product->name ?? null,
                    'quantity' => $return->quantity,
                ],

                // Customer
                'customer' => [
                    'name' => trim(
                        ($return->customer->first_name ?? '') . ' ' .
                            ($return->customer->last_name ?? '')
                    ),
                ],

                // Warehouse
                'warehouse' => [
                    'id'   => $return->warehouse->id ?? null,
                    'name' => $return->warehouse->name ?? null,
                ],

                // Verification / handover
                'handover' => [
                    'manager_signature'     => $return->manager_signature,
                    'agent_signature'       => $return->agent_signature,
                    'handover_confirmed_at' => $return->handover_confirmed_at,
                    'is_verified'           => !is_null($return->handover_confirmed_at),
                ],

                // Timeline
                'timeline' => [
                    'picked_at'         => $return->picked_at,
                    'return_started_at' => $return->return_started_at,
                    'arrived_at_store'  => $return->returned_at,
                ],

                // QR payload
                'qr_data' => $qrData,
            ]
        ]);
    }
}
