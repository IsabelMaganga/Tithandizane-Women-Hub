<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated admin
     */
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $query = $admin->notifications();
        
        // Filter by read status if requested
        if ($request->has('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->read();
            }
        }
        
        $notifications = $query->paginate($request->get('per_page', 20));
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $admin->unreadNotifications()->count(),
            ]);
        }
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $admin = Auth::guard('admin')->user();
        $count = $admin->unreadNotifications()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(AdminNotification $notification)
    {
        // Ensure the notification belongs to the authenticated admin
        if ($notification->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $admin = Auth::guard('admin')->user();
        $admin->unreadNotifications()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy(AdminNotification $notification)
    {
        // Ensure the notification belongs to the authenticated admin
        if ($notification->admin_id !== Auth::guard('admin')->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Create a new notification (helper method for internal use)
     */
    public static function createNotification($adminId, $type, $title, $message, $data = null)
    {
        return AdminNotification::create([
            'admin_id' => $adminId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }
}
