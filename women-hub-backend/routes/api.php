<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\HarassmentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

// Auth routes (public)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public content
Route::get('/hygiene-articles', [ContentController::class, 'hygieneArticles']);
Route::get('/hygiene-articles/{article}', [ContentController::class, 'hygieneArticle']);
Route::get('/general-guides', [ContentController::class, 'generalGuides']);
Route::get('/emergency-contacts', [ContentController::class, 'emergencyContacts']);
Route::get('/mentors', [MentorshipController::class, 'mentors']);

// Anonymous harassment report
Route::post('/harassment-reports/anonymous', [HarassmentController::class, 'store']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);



    // Messaging routes
    Route::post('/messages', [MessageController::class, 'send']);
    Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'getMessages']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations', [MessageController::class, 'getConversations']);


    // Mentorship
    Route::post('/mentorship/request', [MentorshipController::class, 'request']);
    Route::get('/mentorship/my-sessions', [MentorshipController::class, 'mySessions']);
    Route::get('/mentorship/mentor-sessions', [MentorshipController::class, 'mentorSessions']);
    Route::patch('/mentorship/sessions/{session}', [MentorshipController::class, 'updateStatus']);

    // Harassment reports (authenticated)
    Route::post('/harassment-reports', [HarassmentController::class, 'store']);
    Route::get('/harassment-reports/my-reports', [HarassmentController::class, 'myReports']);

    // Admin
    Route::get('/admin/harassment-reports', [HarassmentController::class, 'index']);
    Route::patch('/admin/harassment-reports/{report}', [HarassmentController::class, 'updateStatus']);
    Route::get('/admin/conversations', [MessageController::class, 'getAllGroups']);

    // Group Discovery & Joining
    Route::get('/groups/available', [MessageController::class, 'getAvailableGroups']);
    Route::post('/conversations/{conversationId}/join', [MessageController::class, 'joinGroup']);
});