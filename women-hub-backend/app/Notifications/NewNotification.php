<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Handles in-app notifications stored in the `notifications` table.
 *
 * Routes must be protected by the `auth:sanctum` middleware so that
 * $request->user() is always resolved correctly — no custom resolveUser()
 * needed.
 *
 * Route examples (api.php):
 *
 *   Route::middleware('auth:sanctum')->group(function () {
 *       Route::get('/notifications',          [NotificationController::class, 'index']);
 *       Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
 *       Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
 *       Route::delete('/notifications/{id}',  [NotificationController::class, 'destroy']);
 *   });
 */
class NotificationController extends Controller
{
    // ── GET /api/notifications ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = $request->user();   // always available behind auth:sanctum

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success'      => true,
            'data'         => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    // ── POST /api/notifications/{id}/read ─────────────────────────────────────

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);    // 404 if it belongs to someone else

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ]);
    }

    // ── POST /api/notifications/read-all ──────────────────────────────────────

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }

    // ── DELETE /api/notifications/{id} ────────────────────────────────────────

    public function destroy(Request $request, string $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted.',
        ]);
    }
}