<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\Admin\HarassmentReportController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\Api\GuidanceContentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommunityController;

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

Route::prefix('v1')->group(function () {

    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    // Content
    Route::get('/hygiene-articles',          [ContentController::class, 'hygieneArticles']);
    Route::get('/hygiene-articles/{article}',[ContentController::class, 'hygieneArticle']);
    Route::get('/general-guides',            [ContentController::class, 'generalGuides']);
    Route::get('/emergency-contacts',        [ContentController::class, 'emergencyContacts']);

    // Mentor listings (public)
    Route::get('/mentors/active',            [MentorController::class, 'getActiveMentors']);
    Route::get('/mentor-stats',              [MentorController::class, 'getMentorStats']);
    Route::get('/mentors/{id}',              [MentorController::class, 'getMentorDetails']);

    // Harassment reports (anonymous)
    Route::post('/harassment-reports',           [HarassmentReportController::class, 'store']);
    Route::post('/harassment-reports/anonymous', [HarassmentReportController::class, 'store']);
    Route::get('/harassment-reports/reference/{referenceNumber}', [HarassmentReportController::class, 'showByReference']);

    // Community (public read)
    Route::get('/community/posts', [CommunityController::class, 'index']);

    // ============================================
    // PROTECTED ROUTES (Authentication Required)
    // ============================================

    Route::middleware('auth:sanctum')->group(function () {

        // ── Auth ──────────────────────────────────────────────────────────────
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);

        // ── Incident / NLP ────────────────────────────────────────────────────
        Route::post('/ask', [IncidentController::class, 'incident']);

        // ── Messaging ─────────────────────────────────────────────────────────
        Route::post('/messages',                                   [MessageController::class, 'send']);
        Route::get ('/conversations',                              [MessageController::class, 'getConversations']);
        Route::post('/conversations',                              [MessageController::class, 'createConversation']);
        Route::get ('/conversations/{id}',                         [MessageController::class, 'showInfo']);
        Route::get ('/conversations/{conversationId}/messages',    [MessageController::class, 'getMessages']);
        Route::get ('/groups/available',                           [MessageController::class, 'getAvailableGroups']);
        Route::post('/conversations/{conversationId}/join',        [MessageController::class, 'joinGroup']);

        // ── Users ─────────────────────────────────────────────────────────────
        Route::get('/users',          [UserController::class, 'getAllUsers']);
        Route::get('/users/{userId}', [UserController::class, 'getUser']);

        // ── Mentorship ────────────────────────────────────────────────────────

        // Mentor listings
        Route::get('/mentors',                    [MentorshipController::class, 'mentors']);
        Route::get('/available-mentors',          [MentorshipController::class, 'mentors']);
        Route::get('/mentors/{mentor}/reviews',   [MentorshipController::class, 'mentorReviews']);

        // Mentor availability (mentor only)
        Route::get('/mentor/availability',        [MentorshipController::class, 'myAvailability']);
        Route::put('/mentor/availability',        [MentorshipController::class, 'updateAvailability']);

        // Sessions
        Route::post  ('/mentorship/request',                        [MentorshipController::class, 'request']);
        Route::get   ('/mentorship/my-sessions',                    [MentorshipController::class, 'mySessions']);
        Route::get   ('/mentorship/mentor-sessions',                [MentorshipController::class, 'mentorSessions']);
        Route::patch ('/mentorship/sessions/{session}/status',      [MentorshipController::class, 'updateStatus']);
        Route::post  ('/mentorship/sessions/{session}/start',       [MentorshipController::class, 'startConversation']);
        Route::post  ('/mentorship/sessions/{session}/terminate',   [MentorshipController::class, 'terminateSession']);
        Route::post  ('/mentorship/sessions/{session}/missed',      [MentorshipController::class, 'markMissed']);
        Route::post  ('/mentorship/sessions/{session}/review',      [MentorshipController::class, 'submitReview']);

        // ── Harassment reports (authenticated) ────────────────────────────────
        Route::get('/harassment-reports/my-reports', [HarassmentReportController::class, 'myReports']);

        // ── Notifications ─────────────────────────────────────────────────────
        Route::get   ('/notifications',                  [NotificationController::class, 'getNotifications']);
        Route::patch ('/notifications/mark-all-read',    [NotificationController::class, 'markAllAsRead']);
        Route::patch ('/notifications/{id}/read',        [NotificationController::class, 'markAsRead']);

        // ── Guidance content ──────────────────────────────────────────────────
        Route::get('/content',      [GuidanceContentController::class, 'publicIndex']);
        Route::get('/content/{id}', [GuidanceContentController::class, 'publicShow']);

        // ── Community ─────────────────────────────────────────────────────────
        Route::post('/community/posts',                    [CommunityController::class, 'store']);
        Route::post('/community/posts/{post}/comments',    [CommunityController::class, 'comment']);
        Route::post('/community/posts/{post}/like',        [CommunityController::class, 'like']);

        // ── Mentor content (mentor.api middleware) ────────────────────────────
        Route::prefix('mentor')->middleware('mentor.api')->group(function () {
            Route::get('/content', [GuidanceContentController::class, 'mentorIndex']);
            Route::post('/content', [GuidanceContentController::class, 'store']);
            Route::put('/content/{id}', [GuidanceContentController::class, 'update']);
            Route::patch('/content/{id}/unpublish', [GuidanceContentController::class, 'toggleUnpublish']);
            Route::delete('/content/{id}', [GuidanceContentController::class, 'destroy']);
            Route::get('/harassment-reports', [HarassmentReportController::class, 'mentorHarassmentReports']);
            Route::get('/harassment-reports/{id}', [HarassmentReportController::class, 'mentorHarassmentReport']);
        });

        // ── Admin ─────────────────────────────────────────────────────────────
        Route::prefix('admin')->group(function () {

            // Harassment reports
            Route::get   ('/harassment-reports',          [HarassmentReportController::class, 'index']);
            Route::patch ('/harassment-reports/{report}', [HarassmentReportController::class, 'updateStatus']);

            // Conversations / groups
            Route::get('/conversations', [MessageController::class, 'getAllGroups']);

            // Mentor management
            Route::get   ('/mentors',            [MentorController::class, 'index']);
            Route::post  ('/mentors',            [MentorController::class, 'store']);
            Route::get   ('/mentors/{id}',       [MentorController::class, 'show']);
            Route::put   ('/mentors/{id}',       [MentorController::class, 'update']);
            Route::patch ('/mentors/{id}',       [MentorController::class, 'update']);
            Route::delete('/mentors/{id}',       [MentorController::class, 'destroy']);
            Route::patch ('/mentors/{id}/status',[MentorController::class, 'toggleStatus']);
        });
    });
});

// ============================================
// FALLBACK ROUTE (Must be last)
// ============================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found',
    ], 404);
});