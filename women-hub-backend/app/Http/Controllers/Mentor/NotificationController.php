<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return back();
        }

        return response()->json(['message' => 'Notification not found.'], 404);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }

    public function getMyNotification()
    {
        $notifications = auth()->user()->notifications()->latest()->get();
        return view('mentor.notifications.index', compact('notifications'));
    }




}
