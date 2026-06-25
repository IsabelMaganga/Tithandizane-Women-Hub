<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Resolve the authenticated user's ID across all guards.
     */
    private function resolveUserId(): ?int
    {
        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->id();
        }
        if (auth()->guard('mentor')->check()) {
            return auth()->guard('mentor')->id();
        }
        return auth()->id();
    }

    
    private function normalize(Notification $n): array
    {
        $data = is_array($n->data) ? $n->data : [];

        // Resolve title: column first, then data.title
        $title = $n->title ?? $data['title'] ?? null;

        // Resolve message: column first, then data.message, then data.body
        $message = $n->message ?? $data['message'] ?? $data['body'] ?? null;

        // Resolve report_id: column first, then inside data
        $reportId = $n->report_id ?? $data['report_id'] ?? null;

        // Clean type string (strip PHP namespace from Laravel channel types)
        $type = $data['type'] ?? $n->type ?? null;

        return [
            'id'         => $n->id,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'is_read'    => (bool) $n->is_read,
            'report_id'  => $reportId,
            'data'       => $data,
            'created_at' => $n->created_at,
        ];
    }

    /**
     * GET /notifications
     */
    public function getNotifications(Request $request)
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $rows = Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $notifications = $rows->map(fn($n) => $this->normalize($n))->values();

        $unreadCount = $notifications->filter(fn($n) => !$n['is_read'])->count();

        return response()->json([
            'success'       => true,
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /**
     * PATCH /notifications/{id}/read
     */
    public function markAsRead($id)
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $notification = Notification::where('user_id', $userId)->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    
    public function markAllAsRead()
    {
        $userId = $this->resolveUserId();

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}