<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $mentor = Auth::guard('mentor')->user();
        $notification = $mentor ? $mentor->notifications()->find($id) : null;

        if ($notification) {
            $notification->markAsRead();
            return back();
        }

        return response()->json(['message' => 'Notification not found.'], 404);
    }

    public function markAllAsRead()
    {
        $mentor = Auth::guard('mentor')->user();
        if ($mentor) {
            $mentor->unreadNotifications->markAsRead();
        }

        return back();
    }

    public function getMyNotification()
    {
        $mentor = Auth::guard('mentor')->user();
        $notifications = $mentor ? $mentor->notifications()->latest()->get() : collect();

        return view('mentor.notifications.index', compact('notifications'));
    }




}
