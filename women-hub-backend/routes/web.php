<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HarassmentReportController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Mentor\AuthController as MentorAuthController;
use App\Http\Controllers\Mentor\DashboardController as MentorDashboardController;
use App\Http\Controllers\Mentor\NotificationController;
use App\Http\Controllers\Mentor\SecurityController as MentorSecurityController;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

//home page
Route::get('/', fn() => view('welcome'))->name('welcome');

// get started route - UPDATED to use correct path
Route::get('/get-started', fn() => view('admin.home.get-started'))->name('get.started');

// IMPORTANT: Move this BEFORE the admin routes to avoid conflict
// Or better yet, remove it and handle redirects in middleware
// Route::get('/login', function() { return redirect()->route('get.started'); })->name('login');

// Auth admin routes (guest only)
Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Protected admin routes
Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Mentors
    Route::get('/mentors/toggle-status/{mentor}', [MentorController::class, 'toggleStatus'])->name('mentors.toggle');
    Route::resource('mentors', MentorController::class);
    
    // Harassment Reports
    Route::resource('reports', HarassmentReportController::class)->except(['edit', 'update']);
    Route::patch('/reports/{report}/status', [HarassmentReportController::class, 'updateStatus'])->name('reports.update-status');
});

// Auth mentors routes (guest only)
Route::middleware('guest:mentor')->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/login',  [MentorAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [MentorAuthController::class, 'login'])->name('login.post');
});

// Protected mentor routes
Route::middleware('auth:mentor')->prefix('mentor')->name('mentor.')->group(function () {
    Route::post('/logout', [MentorAuthController::class, 'logout'])->name('logout');
    Route::delete('/sessions', [MentorAuthController::class, 'logoutAllSessions'])->name('logoutAllSessions');
    
    // dashboard
    Route::get('/dashboard',[MentorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/notifications/{id}/read',[NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllAsRead'])->name('notification.read-all');
    
    // appointments
    Route::get('/appointments',[MentorSecurityController::class, 'showAppointments'])->name('appointment');
    
    // calender
    Route::get('/calender',[MentorSecurityController::class, 'showCalender'])->name('calender');
    
    // reports
    Route::get('/reports',[MentorSecurityController::class, 'showReports'])->name('reports');
    
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
});