<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DeliveryNotification;

class NotificationController extends Controller
{
    public function get_notifications(Request $request)
    {
        $user = $request->user();

        if (!$user || strtolower(optional($user->role)->name) !== 'delivery agent') {
            return response()->json([
                'status' => false,
                'message' => 'Access denied'
            ], 403);
        }

        $notifications = DeliveryNotification::where('delivery_agent_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'New Order Received',
            'logged_in_id' => $user->id,
            'data' => $notifications
        ]);
    }

    public function markRead($id)
    {
        DeliveryNotification::where('id', $id)
            ->where('delivery_agent_id', auth()->id())
            ->update(['is_read' => 1]);

        return response()->json(['status' => true]);
    }

    public function markAllRead()
    {
        DeliveryNotification::where('delivery_agent_id', auth()->id())
            ->update(['is_read' => 1]);

        return response()->json(['status' => true]);
    }

    public function getSettings()
    {
        return response()->json([
            'status' => true,
            'data' => auth()->user()->notificationSettings
        ]);
    }

    public function updateSettings(Request $request)
    {
        auth()->user()->notificationSettings()
            ->update($request->only([
                'new_order',
                'updates',
                'chat',
                'promo',
                'app_updates'
            ]));

        return response()->json(['status' => true]);
    }
}
