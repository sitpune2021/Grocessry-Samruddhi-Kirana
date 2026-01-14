<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function get_notifications(Request $request)
    {
        $query = $request->user()->notifications();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'status' => true,
            'data' => $query->latest()->paginate(10)
        ]);
    }

    public function markRead($id)
    {
        auth()->user()->notifications()
            ->where('id', $id)
            ->update(['is_read' => true]);

        return response()->json(['status' => true]);
    }

    public function markAllRead()
    {
        auth()->user()->notifications()->update(['is_read' => true]);
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
        auth()->user()->notificationSettings()->update($request->only([
            'new_order',
            'updates',
            'chat',
            'promo',
            'app_updates'
        ]));

        return response()->json(['status' => true]);
    }
}
