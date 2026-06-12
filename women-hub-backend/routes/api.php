<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\Admin\HarassmentReportController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\MentorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Broadcasting\BroadcastManager;

// Broadcasting auth endpoint for API (token-based auth with Sanctum)
Route::post('/broadcasting/auth', function () {
    return BroadcastManager::auth();
})->middleware(['auth:sanctum'])->withoutMiddleware('api');

// ============================================
// PUBLIC API ROUTES (No Authentication Required)
// ============================================

// Direct routes (without v1 prefix) for frontend compatibility
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Mentor routes - PUBLIC (for React Native frontend)
Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);  // Get active mentors only
Route::get('/mentors/{id}', [MentorController::class, 'getMentorDetails']);     // Get single mentor details
Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);       // Get mentor statistics

// Content routes
Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
Route::get('/general-guides', [ContentController::class, 'generalGuides']);
Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);

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
    Route::get('/mentors/active', [MentorController::class, 'getActiveMentors']);  // Get active mentors only
    Route::get('/mentors/{id}', [MentorController::class, 'getMentorDetails']);     // Get single mentor details
    Route::get('/mentor-stats', [MentorController::class, 'getMentorStats']);       // Get mentor statistics

    // Anonymous harassment report
    Route::post('/harassment-reports/anonymous', [HarassmentController::class, 'store']);

    // ============================================
    // AUTHENTICATED ROUTES (Require Sanctum Token)
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
        Route::post('/harassment-reports', [HarassmentController::class, 'store']);
        Route::get('/harassment-reports/my-reports', [HarassmentController::class, 'myReports']);

        // ============================================
        // ADMIN ROUTES (Require Admin Authentication)
        // ============================================
        Route::prefix('admin')->group(function () {
            // Harassment reports management
            Route::get('/harassment-reports', [HarassmentController::class, 'index']);
            Route::patch('/harassment-reports/{report}', [HarassmentController::class, 'updateStatus']);
            
            // Groups management
            Route::get('/conversations', [MessageController::class, 'getAllGroups']);
            
            // ============================================
            // MENTOR MANAGEMENT - ADMIN API ENDPOINTS
            // ============================================
            // Get all mentors (admin view - includes inactive/pending)
            Route::get('/mentors', [MentorController::class, 'index']);
            
            // Get single mentor (admin view)
            Route::get('/mentors/{id}', [MentorController::class, 'show']);
            
            // Create new mentor
            Route::post('/mentors', [MentorController::class, 'store']);
            
            // Update mentor
            Route::put('/mentors/{id}', [MentorController::class, 'update']);
            Route::patch('/mentors/{id}', [MentorController::class, 'update']);
            
            // Delete mentor
            Route::delete('/mentors/{id}', [MentorController::class, 'destroy']);
            
            // Update mentor status (activate/deactivate)
            Route::patch('/mentors/{id}/status', [MentorController::class, 'toggleStatus']);
        });

        // Group Discovery & Joining
        Route::get('/groups/available', [MessageController::class, 'getAvailableGroups']);
        Route::post('/conversations/{conversationId}/join', [MessageController::class, 'joinGroup']);
    });

    // Public routes (no authentication required)
Route::post('/harassment-reports', [HarassmentReportController::class, 'store']);
Route::post('/harassment-reports/anonymous', [HarassmentReportController::class, 'store']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/harassment-reports/my-reports', [HarassmentReportController::class, 'myReports']);
});

});

// ============================================
// FALLBACK ROUTE FOR 404 ERRORS (Optional)
// ============================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});