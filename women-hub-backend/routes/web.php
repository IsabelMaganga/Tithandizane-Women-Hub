<?php

use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Support\Facades\{Broadcast, Route};

use App\Http\Controllers\Admin\{AuthController, DashboardController, HarassmentReportController, MentorController, SettingsController};
use App\Http\Controllers\Mentor\{AuthController as MentorAuthController, CalenderController, DashboardController as MentorDashboardController, NotificationController, ReportController, SecurityController as MentorSecurityController};

// Home page
Route::get('/', fn() => view('welcome'))->name('welcome');

// CSS test route (no authentication required)
Route::get('/test-css', fn() => view('test-css'))->name('test.css');

// Get started route
Route::get('/get-started', fn() => view('get-started'))->name('get.started');
Route::get('/login?redirect/', fn() => view('get-started'))->name('login');

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
    // HARASSMENT REPORTS ROUTES - UPDATED
    // ============================================
    Route::prefix('reports')->name('reports.')->group(function () {
        // Export route (must come before {report} parameter)
        Route::get('/export', [HarassmentReportController::class, 'export'])->name('export');
        Route::get('/export/csv', [HarassmentReportController::class, 'export'])->name('export.csv'); // Alias for compatibility
        
        // JSON endpoint for AJAX - GET request
        Route::get('/{id}/json', [HarassmentReportController::class, 'show'])->name('json');
        
        // Status update routes
        Route::patch('/{id}/status', [HarassmentReportController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/respond', [HarassmentReportController::class, 'respond'])->name('respond');
        
        // Regular show route (HTML view)
        Route::get('/{id}', [HarassmentReportController::class, 'show'])->name('show');
    });
    
    // Harassment Reports Resource route
    Route::resource('reports', HarassmentReportController::class)->only(['index', 'destroy']);
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

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});