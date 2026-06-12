<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\Admin\HarassmentReportController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

// ============================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================

// Direct routes (without v1 prefix) for frontend compatibility
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Mentor routes - PUBLIC (for React Native frontend)
Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);
Route::get('/mentors/{id}', [MentorController::class, 'getMentorDetails']);
Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);

// Content routes
Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
Route::get('/general-guides', [ContentController::class, 'generalGuides']);
Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);

// Harassment report routes (public - anonymous)
Route::post('/harassment-report/submit', [HarassmentReportController::class, 'submitReport']);
Route::post('/harassment-report/anonymous', [HarassmentReportController::class, 'submitAnonymousReport']);

// ============================================
// API V1 ROUTES
// ============================================

Route::prefix('v1')->group(function () {
    
    // Auth routes (public)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    
    // Public content
    Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
    Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
    Route::get('/general-guides', [ContentController::class, 'generalGuides']);
    Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);
    
    // Mentor routes - PUBLIC (for React Native frontend)
    Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);
    Route::get('/mentors/{id}', [MentorController::class, 'getMentorDetails']);
    Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);
    
    // Anonymous harassment report (public)
    Route::post('/harassment-reports/anonymous', [HarassmentReportController::class, 'store']);
    Route::post('/harassment-reports', [HarassmentReportController::class, 'store']);
    
    // ============================================
    // PROTECTED ROUTES (Authentication Required)
    // ============================================
    
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        
        // Messaging routes
        Route::post('/messages', [MessageController::class, 'send']);
        Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
        Route::post('/conversations', [MessageController::class, 'createConversation']);
        Route::get('/conversations', [MessageController::class, 'getConversations']);
        Route::get('/conversations/{id}', [MessageController::class, 'showInfo']);
        
        // User routes
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::get('/users/{userId}', [UserController::class, 'getUser']);
        
        // Mentorship routes
        Route::get('/available-mentors', [MentorshipController::class, 'mentors']);
        Route::post('/mentorship/request', [MentorshipController::class, 'request']);
        Route::get('/mentorship/my-sessions', [MentorshipController::class, 'mySessions']);
        Route::get('/mentorship/mentor-sessions', [MentorshipController::class, 'mentorSessions']);
        Route::patch('/mentorship/sessions/{session}', [MentorshipController::class, 'updateStatus']);
        
        // Harassment reports (authenticated)
        Route::get('/harassment-reports/my-reports', [HarassmentReportController::class, 'myReports']);
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'getNotifications']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        
        // ============================================
        // ADMIN ROUTES
        // ============================================
        
        Route::prefix('admin')->group(function () {
            
            // Harassment reports management
            Route::get('/harassment-reports', [HarassmentReportController::class, 'index']);
            Route::patch('/harassment-reports/{report}', [HarassmentReportController::class, 'updateStatus']);
            
            // Groups management
            Route::get('/conversations', [MessageController::class, 'getAllGroups']);
            
            // Mentor management (admin view - includes inactive/pending)
            Route::get('/mentors', [MentorController::class, 'index']);
            Route::get('/mentors/{id}', [MentorController::class, 'show']);
            Route::post('/mentors', [MentorController::class, 'store']);
            Route::put('/mentors/{id}', [MentorController::class, 'update']);
            Route::patch('/mentors/{id}', [MentorController::class, 'update']);
            Route::delete('/mentors/{id}', [MentorController::class, 'destroy']);
            Route::patch('/mentors/{id}/status', [MentorController::class, 'toggleStatus']);
        });
        
        // Group Discovery & Joining
        Route::get('/groups/available', [MessageController::class, 'getAvailableGroups']);
        Route::post('/conversations/{conversationId}/join', [MessageController::class, 'joinGroup']);
    });
});

// ============================================
// FALLBACK ROUTE (Must be last)
// ============================================

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});