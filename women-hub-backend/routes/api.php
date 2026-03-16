<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MentorshipController;
use App\Http\Controllers\HarassmentReportController;
use App\Http\Controllers\ContentController;
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
Route::post('/harassment-reports/anonymous', [HarassmentReportController::class, 'store']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Mentorship
    Route::post('/mentorship/request', [MentorshipController::class, 'request']);
    Route::get('/mentorship/my-sessions', [MentorshipController::class, 'mySessions']);
    Route::get('/mentorship/mentor-sessions', [MentorshipController::class, 'mentorSessions']);
    Route::patch('/mentorship/sessions/{session}', [MentorshipController::class, 'updateStatus']);

    // Harassment reports (authenticated)
    Route::post('/harassment-reports', [HarassmentReportController::class, 'store']);
    Route::get('/harassment-reports/my-reports', [HarassmentReportController::class, 'myReports']);

    // Admin
    Route::get('/admin/harassment-reports', [HarassmentReportController::class, 'index']);
    Route::patch('/admin/harassment-reports/{report}', [HarassmentReportController::class, 'updateStatus']);
});