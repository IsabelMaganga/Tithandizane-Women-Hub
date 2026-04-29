<?php

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\{Broadcast, Route};
use App\Http\Controllers\Admin\{AuthController, DashboardController, HarassmentReportController, MentorController};
use App\Http\Controllers\Mentor\{AuthController as MentorAuthController, DashboardController as MentorDashboardController, NotificationController, ReportController, SecurityController as MentorSecurityController};

// Home page
Route::get('/', fn() => view('welcome'))->name('welcome');

// CSS test route (no authentication required)
Route::get('/test-css', fn() => view('test-css'))->name('test.css');

// Get started route
Route::get('/get-started', fn() => view('get-started'))->name('get.started');

// Auth admin routes (guest only)
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');
});

// Protected admin routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Mentors - Full resource route
    Route::resource('mentors', MentorController::class);

    // Additional mentor routes
    Route::patch('/mentors/{mentor}/toggle-status', [MentorController::class, 'toggleStatus'])->name('mentors.toggle-status');

    // Harassment Reports
    Route::resource('reports', HarassmentReportController::class)->except(['edit', 'update']);
    Route::patch('/reports/{report}/status', [HarassmentReportController::class, 'updateStatus'])->name('reports.update-status');
});

// Auth mentors routes (guest only)
Route::middleware('guest:mentor')->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/auth/login',  [MentorAuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [MentorAuthController::class, 'login'])->name('login.post');
});

// Protected mentor routes
Route::middleware('auth:mentor')->prefix('mentor')->name('mentor.')->group(function () {

    // auth()->user()->markEmailAsVerified();
    Route::post('/logout', [MentorAuthController::class, 'logout'])->name('logout');
    Route::delete('/sessions', [MentorAuthController::class, 'logoutAllSessions'])->name('logoutAllSessions');

    // Route::get('/auth/forgot',  [MentorAuthController::class, 'showForgotPassword'])->name('forgot');

    // dashboard
    Route::get('/dashboard',[MentorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{id}/read',[NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllAsRead'])->name('notification.read-all');

    // appointments
    Route::get('/appointments',[MentorSecurityController::class, 'showAppointments'])->name('appointment');

    // calender
    Route::get('/calender',[MentorSecurityController::class, 'showCalender'])->name('calender');

    // reports
    Route::get('/reports',[ReportController::class, 'showReports'])->name('reports');
    Route::post('/reports',[ReportController::class, 'SubmitReport'])->name('submit.report');
    Route::get('/my-reports',[ReportController::class, 'showPending'])->name('pending.reports');

    // chat
    Route::get('/chats',[MentorSecurityController::class, 'showChat'])->name('chat');
    Route::get('/groups',[MentorSecurityController::class, 'showChatGroups'])->name('groups');
    Route::get('/group',[MentorSecurityController::class, 'showGroupForm'])->name('group');

    // profile section
    Route::get('/profile', [MentorSecurityController::class, 'showMyProfile'])->name('profile');

    // guidance routes
    Route::get('/guidance', [MentorSecurityController::class, 'showGuidance'])->name('Guidance');
    Route::get('/guidance/hygiene', [MentorSecurityController::class, 'showHygiene'])->name('hygiene');
    Route::get('/guidance/general', [MentorSecurityController::class, 'showGeneral'])->name('general');
    Route::get('/guidance/emergency', [MentorSecurityController::class, 'showEmergency'])->name('emergency');

    // settings related routes
    Route::get('/settings', [MentorSecurityController::class, 'showSettings'])->name('settings');
    Route::get('/settings/profile', [MentorSecurityController::class, 'showProfile'])->name('showProfile');
    Route::put('/settings/profile', [MentorSecurityController::class, 'updateProfile'])->name('updateProfile');
    Route::get('/settings/security', [MentorSecurityController::class, 'showSecurity'])->name('showSecurity');
    Route::put('/settings/security', [MentorSecurityController::class, 'updateSecurity'])->name('updateSecurity');
    // Route::get('/test-broadcast', [MentorDashboardController::class, 'testBroadcast']);
});
