<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helper\ResponseHelper;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        $validated = $request->validate([
            "user_id" => "required|exists:users,id"
        ]);

        $notifications = DB::table('notifications as n')
            ->where('n.user_id', '=', $validated['user_id'])
            ->leftJoin('users as s', 'n.sender_id', 's.id')
            ->leftJoin('users as r', 'n.user_id', 'r.id')
            ->select(
                'n.id',
                'n.title',
                'n.body',
                'r.nickname as receiver',
                's.nickname as sender',
                'n.status',
                's.avatar as sender_avatar',
                'n.receiver as company',
                'n.created_at'
            )
            ->orderBy('n.id', 'desc')
            ->get();

        if ($notifications->isEmpty()) {
            return ResponseHelper::error("No notification found", "Failed", 404);
        }

        $notifications->each(function ($notification) {
            $notification->sender_avatar = env('CURRENT_URL') . Storage::url($notification->sender_avatar);
        });

        return ResponseHelper::success(
            $notifications,
            "Successfully fetched notifications",
            "success",
            200
        );
    }

    public function unreadNotificationCount(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:notifications,user_id'
        ]);

        $notifications = DB::table('notifications as n')
            ->where('n.user_id', '=', $validated['user_id'])
            ->where('n.status', 'unread')
            ->count();

        if (!$notifications) {
            return ResponseHelper::error("No notification found", "Failed", 404);
        }

        return ResponseHelper::success(
            $notifications,
            "Successfully fetched notifications",
            "success",
            200
        );
    }

    public function readNotification(Request $request)
    {
        $validated = $request->validate([
            'id' => "required|exists:notifications,id"
        ]);

        $notification = DB::table('notifications as n')
            ->where('n.id', $validated['id'])
            ->update(['status' => 'read']);

        return ResponseHelper::success(
            $notification,
            "Successfully",
            "success",
            200
        );
    }

    public function deleteNotification(Request $request)
    {
        $validated = $request->validate([
            'id' => "required|exists:notifications,id"
        ]);

        $notification = DB::table('notifications as n')
            ->where('n.id', $validated['id'])
            ->delete();

        return ResponseHelper::success(
            $notification,
            "Successfully",
            "success",
            200
        );
    }
}
