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
// PUBLIC API ROUTES (No Authentication Required)
// ============================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Mentor routes - PUBLIC
Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);
Route::get('/mentors/{id}', [MentorController::class, 'getMentorDetails']);
Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);
Route::get('/community/posts', [CommunityController::class, 'index']);

// Content routes
Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
Route::get('/general-guides', [ContentController::class, 'generalGuides']);
Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);

// Harassment report routes (public - anonymous)
Route::post('/harassment-report/submit', [HarassmentReportController::class, 'submitReport']);
Route::post('/harassment-report/anonymous', [HarassmentReportController::class, 'submitAnonymousReport']);
 Route::post('/community/posts', [CommunityController::class, 'store']);

// Guidance content (authenticated users) - outside v1 prefix for compatibility
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/content', [GuidanceContentController::class, 'publicIndex']);
    Route::get('/content/{id}', [GuidanceContentController::class, 'publicShow']);

    Route::prefix('mentor')->middleware('mentor.api')->group(function () {
        Route::get('/content', [GuidanceContentController::class, 'mentorIndex']);
        Route::post('/content', [GuidanceContentController::class, 'store']);
        Route::put('/content/{id}', [GuidanceContentController::class, 'update']);
        Route::patch('/content/{id}/unpublish', [GuidanceContentController::class, 'toggleUnpublish']);
        Route::delete('/content/{id}', [GuidanceContentController::class, 'destroy']);
    });
});

// ============================================
// API V1 ROUTES
// ============================================

Route::prefix('v1')->group(function () {

    // ── Public ────────────────────────────────────────────────────────────────

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
    Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
    Route::get('/general-guides', [ContentController::class, 'generalGuides']);
    Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);

    // Mentor routes - PUBLIC
    Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);
    Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);

    // Anonymous harassment reports
    Route::post('/harassment-reports/anonymous', [HarassmentReportController::class, 'store']);
    Route::post('/harassment-reports', [HarassmentReportController::class, 'store']);
    Route::get('/harassment-reports/reference/{referenceNumber}', [HarassmentReportController::class, 'showByReference']);

    Route::post('/ask', [IncidentController::class, 'incident']);

   

    // ── Protected ─────────────────────────────────────────────────────────────

    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // ── Messaging ────────────────────────────────────────────────────────
        Route::post('/messages', [MessageController::class, 'send']);
        Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
        Route::post('/conversations', [MessageController::class, 'createConversation']);
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::get('/conversations/{id}', [MessageController::class, 'showInfo']);

        // Group discovery & joining
        Route::get('/groups/available', [MessageController::class, 'getAvailableGroups']);
        Route::post('/conversations/{conversationId}/join', [MessageController::class, 'joinGroup']);

        // ── Users ────────────────────────────────────────────────────────────
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::get('/users/{userId}', [UserController::class, 'getUser']);

        // ── Mentorship ───────────────────────────────────────────────────────

        // Mentor listings
        Route::get('/available-mentors', [MentorshipController::class, 'mentors']);
        Route::get('/mentors', [MentorshipController::class, 'mentors']);
        Route::get('/mentors/{mentor}/reviews', [MentorshipController::class, 'mentorReviews']);

        // Session management
        Route::post('/mentorship/request', [MentorshipController::class, 'request']);
        Route::get('/mentorship/my-sessions', [MentorshipController::class, 'mySessions']);
        Route::get('/mentorship/mentor-sessions', [MentorshipController::class, 'mentorSessions']);
        Route::patch('/mentorship/sessions/{session}/status', [MentorshipController::class, 'updateStatus']);
        Route::post('/mentorship/sessions/{session}/start', [MentorshipController::class, 'startConversation']);
        Route::post('/mentorship/sessions/{session}/review', [MentorshipController::class, 'submitReview']);
        Route::post('/mentorship/sessions/{session}/start', [MentorshipController::class, 'startConversation']);
        Route::post('/mentorship/sessions/{session}/terminate', [MentorshipController::class, 'terminateSession']);
        Route::post('/mentorship/sessions/{session}/review', [MentorshipController::class, 'submitReview']);

        // ── Harassment reports (authenticated) ───────────────────────────────
        Route::get('/harassment-reports/my-reports', [HarassmentReportController::class, 'myReports']);

        // ── Notifications ────────────────────────────────────────────────────
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

        // ── Guidance content ─────────────────────────────────────────────────
        Route::get('/content', [GuidanceContentController::class, 'publicIndex']);
        Route::get('/content/{id}', [GuidanceContentController::class, 'publicShow']);

        //------───community routes────────────────────────
        Route::get('/community/posts', [CommunityController::class, 'index']);
        Route::post('/community/posts', [CommunityController::class, 'store']);
        Route::post('/community/posts/{post}/comments', [CommunityController::class, 'comment']);
        Route::post('/community/posts/{post}/like', [CommunityController::class, 'like']);

        Route::prefix('mentor')->middleware('mentor.api')->group(function () {
            Route::get('/content', [GuidanceContentController::class, 'mentorIndex']);
            Route::post('/content', [GuidanceContentController::class, 'store']);
            Route::put('/content/{id}', [GuidanceContentController::class, 'update']);
            Route::patch('/content/{id}/unpublish', [GuidanceContentController::class, 'toggleUnpublish']);
            Route::delete('/content/{id}', [GuidanceContentController::class, 'destroy']);
            Route::get('/harassment-reports', [HarassmentReportController::class, 'mentorHarassmentReports']);
            Route::get('/harassment-reports/{id}', [HarassmentReportController::class, 'mentorHarassmentReport']);
        });

        // ── Admin ────────────────────────────────────────────────────────────
        Route::prefix('admin')->group(function () {

            // Harassment reports
            Route::get('/harassment-reports', [HarassmentReportController::class, 'index']);
            Route::patch('/harassment-reports/{report}', [HarassmentReportController::class, 'updateStatus']);

            // Groups
            Route::get('/conversations', [MessageController::class, 'getAllGroups']);

            // Mentor management
            Route::get('/mentors', [MentorController::class, 'index']);
            Route::get('/mentors/{id}', [MentorController::class, 'show']);
            Route::post('/mentors', [MentorController::class, 'store']);
            Route::put('/mentors/{id}', [MentorController::class, 'update']);
            Route::patch('/mentors/{id}', [MentorController::class, 'update']);
            Route::delete('/mentors/{id}', [MentorController::class, 'destroy']);
            Route::patch('/mentors/{id}/status', [MentorController::class, 'toggleStatus']);
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