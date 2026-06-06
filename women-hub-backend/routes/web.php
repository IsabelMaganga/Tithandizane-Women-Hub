<?php

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\{Broadcast, Route};

use App\Http\Controllers\Admin\{AuthController, DashboardController, HarassmentReportController, MentorController, SettingsController, UserController};
use App\Http\Controllers\Mentor\{AuthController as MentorAuthController, CalenderController, DashboardController as MentorDashboardController, NotificationController, ReportController, SecurityController as MentorSecurityController};
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\HarassmentReportController as UserHarassmentReportController;

// Home page
Route::get('/', fn() => view('welcome'))->name('welcome');

// CSS test route (no authentication required)
Route::get('/test-css', fn() => view('test-css'))->name('test.css');

// Get started route
Route::get('/get-started', fn() => view('get-started'))->name('get.started');
Route::get('/login?redirect/', fn() => view('get-started'))->name('login');

// ============================================
// USER HARASSMENT REPORT ROUTES (Public/API)
// ============================================
Route::prefix('api')->name('api.')->group(function () {
    // User report submission routes
    Route::post('/harassment-report', [UserHarassmentReportController::class, 'store'])->name('harassment-report.store');
    Route::post('/anonymous-report', [UserHarassmentReportController::class, 'submitAnonymousReport'])->name('anonymous-report.store');
    
    // Protected user report routes (requires authentication)
    Route::middleware('auth:web')->group(function () {
        Route::get('/my-reports', [UserHarassmentReportController::class, 'userReports'])->name('my-reports');
    });
});

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

    // Settings Routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/general', [SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/security', [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::post('/settings/backup', [SettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::delete('/settings/backup/{file}', [SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    Route::post('/settings/api/generate', [SettingsController::class, 'generateApiKey'])->name('settings.api.generate');
    Route::delete('/settings/api/{key}', [SettingsController::class, 'revokeApiKey'])->name('settings.api.revoke');
    Route::post('/settings/email/template/update', [SettingsController::class, 'updateEmailTemplate'])->name('settings.email.template.update');
    Route::post('/settings/guidance/store', [SettingsController::class, 'storeGuidance'])->name('settings.guidance.store');
    Route::put('/settings/guidance/{id}', [SettingsController::class, 'updateGuidance'])->name('settings.guidance.update');
    Route::delete('/settings/guidance/{id}', [SettingsController::class, 'deleteGuidance'])->name('settings.guidance.delete');
    
    // Admin Management Routes
    Route::post('/settings/admins', [SettingsController::class, 'storeAdmin'])->name('settings.admins.store');
    Route::put('/settings/admins/{id}', [SettingsController::class, 'updateAdmin'])->name('settings.admins.update');
    Route::delete('/settings/admins/{id}', [SettingsController::class, 'deleteAdmin'])->name('settings.admins.delete');

    // Mentors - Full resource route
    Route::resource('mentors', MentorController::class);

    // Additional mentor routes
    Route::patch('/mentors/{mentor}/toggle-status', [MentorController::class, 'toggleStatus'])->name('mentors.toggle-status');

    // ============================================
    // USER MANAGEMENT ROUTES
    // ============================================
    Route::prefix('users')->name('users.')->group(function () {
        // Main index route (display all users)
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // Get user details for AJAX (must come before {id} parameter)
        Route::get('/{id}/json', [UserController::class, 'show'])->name('json');
        
        // Update user status
        Route::put('/{id}/status', [UserController::class, 'updateStatus'])->name('update-status');
        
        // Delete user
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
        
        // Regular show route (if you want a detailed view page)
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
    });

    // ============================================
    // HARASSMENT REPORTS ROUTES - FIXED
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        // Main index route (GET)
        Route::get('/', [HarassmentReportController::class, 'index'])->name('index');
        
        // Export routes (must come before {report} parameter)
        Route::get('/export', [HarassmentReportController::class, 'exportReports'])->name('export');
        Route::get('/export/csv', [HarassmentReportController::class, 'exportReports'])->name('export.csv');
        
        // Get available mentors for assignment (AJAX)
        Route::get('/available-mentors', [HarassmentReportController::class, 'getAvailableMentors'])->name('mentors');
        
        // JSON endpoint for AJAX - GET request (must come before {report} parameter)
        Route::get('/{report}/json', [HarassmentReportController::class, 'show'])->name('json');
        
        // Assign mentor to report (POST)
        Route::post('/{report}/assign', [HarassmentReportController::class, 'assignMentor'])->name('assign');
        
        // Respond to report (POST)
        Route::post('/{report}/respond', [HarassmentReportController::class, 'respondToReport'])->name('respond');
        
        // Update report status (PATCH)
        Route::patch('/{report}/status', [HarassmentReportController::class, 'updateStatus'])->name('update-status');
        
        // Regular show route (HTML view) - must come after other routes with parameters
        Route::get('/{report}', [HarassmentReportController::class, 'show'])->name('show');
        
        // Delete report (DELETE)
        Route::delete('/{report}', [HarassmentReportController::class, 'destroy'])->name('destroy');
    });
    
    // Harassment Reports Resource route (for any remaining methods)
    Route::resource('reports', HarassmentReportController::class)->only(['index', 'show', 'destroy']);
});

// ============================================
// MENTOR ROUTES
// ============================================

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

    // dashboard
    Route::get('/dashboard',[MentorDashboardController::class, 'index'])->name('dashboard');

    // notifications routes
    Route::post('/notifications/{id}/read',[NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notifications/read-all',[NotificationController::class, 'markAllAsRead'])->name('notification.read-all');
    Route::get('/notifications',[NotificationController::class, 'getMyNotification'])->name('notifications');

    // appointments
    Route::get('/appointments',[MentorSecurityController::class, 'showAppointments'])->name('appointment');

    // calendar
    Route::get('/calender',[CalenderController::class, 'showCalender'])->name('calender');
    Route::get('/calendar', [CalenderController::class, 'index'])->name('calendar.index');
    Route::get('/events', [CalenderController::class, 'getEvents'])->name('events.fetch');
    Route::post('/events/store', [CalenderController::class, 'store'])->name('events.store');
    Route::delete('/events/{id}', [CalenderController::class, 'destroy'])->name('events.destroy');

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
});

// Fallback route for 404 errors
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});