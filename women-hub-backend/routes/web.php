<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\{AuthController, DashboardController, HarassmentReportController, MentorController, SettingsController, UserController};
use App\Http\Controllers\Mentor\{AuthController as MentorAuthController, CalenderController, ChatController, DashboardController as MentorDashboardController, GuidanceContentController, NotificationController, ReportController, SecurityController as MentorSecurityController,AvailabilityController};
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\HarassmentReportController as UserHarassmentReportController;


// ============================================
// PUBLIC ROUTES
// ============================================

Route::get('/', fn() => view('welcome'))->name('welcome');
Route::get('/test-css', fn() => view('test-css'))->name('test.css');
Route::get('/get-started', fn() => view('get-started'))->name('get.started');

// Unified staff portal login (admin + mentor)
Route::get('/portal/login',  [\App\Http\Controllers\PortalAuthController::class, 'showLogin'])->name('portal.login');
Route::post('/portal/login', [\App\Http\Controllers\PortalAuthController::class, 'login'])->name('portal.login.post');

// Default unauthenticated redirect target (used by auth middleware)
Route::get('/login', fn() => redirect()->route('portal.login'))->name('login');


// ============================================
// USER HARASSMENT REPORT ROUTES (Public/API)
// ============================================

Route::prefix('api')->name('api.')->group(function () {
    Route::post('/harassment-report', [UserHarassmentReportController::class, 'store'])->name('harassment-report.store');
    Route::post('/anonymous-report', [UserHarassmentReportController::class, 'submitAnonymousReport'])->name('anonymous-report.store');

    Route::middleware('auth:web')->group(function () {
        Route::get('/my-reports', [UserHarassmentReportController::class, 'userReports'])->name('my-reports');
    });
});


// ============================================
// ADMIN AUTH ROUTES (guest only)
// ============================================

Route::middleware('guest:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/auth/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/auth/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');
});


// ============================================
// PROTECTED ADMIN ROUTES
// ============================================

Route::middleware('auth:admin')->prefix('admin')->name('admin.')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Settings ─────────────────────────────────────────────────────────────

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',         [SettingsController::class, 'index'])->name('index');

        Route::get('/general',  [SettingsController::class, 'general'])->name('general');
        Route::put('/general',  [SettingsController::class, 'updateGeneral'])->name('general.update');

        Route::get('/admins',   [SettingsController::class, 'admins'])->name('admins');
        Route::post('/admins',  [SettingsController::class, 'storeAdmin'])->name('admins.store');
        Route::put('/admins/{id}',    [SettingsController::class, 'updateAdmin'])->name('admins.update');
        Route::delete('/admins/{id}', [SettingsController::class, 'deleteAdmin'])->name('admins.delete');

        Route::get('/email',        [SettingsController::class, 'email'])->name('email');
        Route::put('/email/{id}',   [SettingsController::class, 'updateEmail'])->name('email.update');

        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::put('/security', [SettingsController::class, 'updateSecurity'])->name('security.update');

        Route::get('/backup',                       [SettingsController::class, 'backup'])->name('backup');
        Route::post('/backup/run',                  [SettingsController::class, 'runBackup'])->name('backup.run');
        Route::get('/backup/download/{file}',       [SettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{file}',             [SettingsController::class, 'deleteBackup'])->name('backup.delete');
    });

    // Backward-compat settings aliases
    Route::post('/settings/general',                [SettingsController::class, 'updateGeneral'])->name('settings.general.update');
    Route::post('/settings/security',               [SettingsController::class, 'updateSecurity'])->name('settings.security.update');
    Route::post('/settings/backup',                 [SettingsController::class, 'runBackup'])->name('settings.backup.create');
    Route::delete('/settings/backup/{file}',        [SettingsController::class, 'deleteBackup'])->name('settings.backup.delete');
    Route::post('/settings/api/generate',           [SettingsController::class, 'generateApiKey'])->name('settings.api.generate');
    Route::delete('/settings/api/{key}',            [SettingsController::class, 'revokeApiKey'])->name('settings.api.revoke');
    Route::post('/settings/email/template/update',  [SettingsController::class, 'updateEmail'])->name('settings.email.template.update');
    Route::post('/settings/guidance/store',         [SettingsController::class, 'storeGuidance'])->name('settings.guidance.store');
    Route::put('/settings/guidance/{id}',           [SettingsController::class, 'updateGuidance'])->name('settings.guidance.update');
    Route::delete('/settings/guidance/{id}',        [SettingsController::class, 'deleteGuidance'])->name('settings.guidance.delete');

    // ── Mentors ───────────────────────────────────────────────────────────────

    Route::resource('mentors', MentorController::class);
    Route::patch('/mentors/{mentor}/toggle-status', [MentorController::class, 'toggleStatus'])->name('mentors.toggle-status');

    // ── Notifications (AJAX) ──────────────────────────────────────────────────

    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications');

    // ── Users ─────────────────────────────────────────────────────────────────

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/',             [UserController::class, 'index'])->name('index');
        Route::get('/{id}/json',    [UserController::class, 'show'])->name('json');
        Route::put('/{id}/status',  [UserController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{id}',      [UserController::class, 'destroy'])->name('destroy');
        Route::get('/{id}',         [UserController::class, 'show'])->name('show');
    });

    // ── Harassment Reports ────────────────────────────────────────────────────

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                         [HarassmentReportController::class, 'index'])->name('index');
        Route::get('/export',                   [HarassmentReportController::class, 'exportReports'])->name('export');
        Route::get('/export/csv',               [HarassmentReportController::class, 'exportReports'])->name('export.csv');
        Route::get('/available-mentors',        [HarassmentReportController::class, 'getAvailableMentors'])->name('mentors');
        Route::get('/{report}/json',            [HarassmentReportController::class, 'show'])->name('json');
        Route::post('/{report}/assign',         [HarassmentReportController::class, 'assignMentor'])->name('assign');
        Route::post('/{report}/respond',        [HarassmentReportController::class, 'respondToReport'])->name('respond');
        Route::patch('/{report}/status',        [HarassmentReportController::class, 'updateStatus'])->name('update-status');
        Route::get('/{report}',                 [HarassmentReportController::class, 'show'])->name('show');
        Route::delete('/{report}',              [HarassmentReportController::class, 'destroy'])->name('destroy');
    });

    Route::resource('reports', HarassmentReportController::class)->only(['index', 'show', 'destroy']);

    // ── Analytics ─────────────────────────────────────────────────────────────

    Route::get('/analytics',              [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export-pdf',   [App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export-pdf');
    Route::get('/analytics/export-excel', [App\Http\Controllers\Admin\AnalyticsController::class, 'exportExcel'])->name('analytics.export-excel');

    // ── Events Calendar ───────────────────────────────────────────────────────

    Route::get('/events',               [App\Http\Controllers\Admin\EventController::class, 'index'])->name('events.index');
    Route::get('/events/data',          [App\Http\Controllers\Admin\EventController::class, 'getEvents'])->name('events.data');
    Route::get('/events/create',        [App\Http\Controllers\Admin\EventController::class, 'create'])->name('events.create');
    Route::post('/events',              [App\Http\Controllers\Admin\EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit',  [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}',       [App\Http\Controllers\Admin\EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}',    [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('events.destroy');

    // ── Admin Notifications ───────────────────────────────────────────────────

    Route::get('/notifications',                                    [App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count',                       [App\Http\Controllers\Admin\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/mark-read',          [App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read',                     [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}',                  [App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
});


// ============================================
// MENTOR AUTH ROUTES (guest only)
// ============================================

Route::middleware('guest:mentor')->prefix('mentor')->name('mentor.')->group(function () {
    Route::get('/auth/login',  [MentorAuthController::class, 'showLogin'])->name('login');
    Route::post('/auth/login', [MentorAuthController::class, 'login'])->name('login.post');
});


// ============================================
// PROTECTED MENTOR ROUTES
// ============================================

Route::middleware('auth:mentor')->prefix('mentor')->name('mentor.')->group(function () {

    // Auth
    Route::post('/logout',    [MentorAuthController::class, 'logout'])->name('logout');
    Route::delete('/sessions', [MentorAuthController::class, 'logoutAllSessions'])->name('logoutAllSessions');

    // Dashboard
    Route::get('/dashboard', [MentorDashboardController::class, 'index'])->name('dashboard');


    //getting and changing available days
    Route::get('/availability', [AvailabilityController::class, 'index'])
            ->name('availability');
    Route::post('/availability', [AvailabilityController::class, 'update'])
            ->name('availability.update');

    // ── Notifications ─────────────────────────────────────────────────────────

    Route::get('/notifications',              [NotificationController::class, 'getMyNotification'])->name('notifications');
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllAsRead'])->name('notification.read-all');

    // ── Appointments ──────────────────────────────────────────────────────────

    Route::get('/appointments',                       [MentorSecurityController::class, 'showAppointments'])->name('appointment');
    Route::patch('/appointments/{session}/cancel',    [MentorSecurityController::class, 'cancelSession'])->name('appointment.cancel');
    Route::patch('/appointments/{session}/accept',    [MentorSecurityController::class, 'acceptSession'])->name('appointment.accept');

    // ── Calendar ──────────────────────────────────────────────────────────────

    Route::get('/calender',         [CalenderController::class, 'showCalender'])->name('calender');
    Route::get('/calendar',         [CalenderController::class, 'index'])->name('calendar.index');
    Route::get('/events',           [CalenderController::class, 'getEvents'])->name('events.fetch');
    Route::post('/events/store',    [CalenderController::class, 'store'])->name('events.store');
    Route::delete('/events/{id}',   [CalenderController::class, 'destroy'])->name('events.destroy');

    // ── Reports (mentor's own) ────────────────────────────────────────────────

    Route::get('/reports',   [ReportController::class, 'showReports'])->name('reports');
    Route::post('/reports',  [ReportController::class, 'SubmitReport'])->name('submit.report');
    Route::get('/my-reports', [ReportController::class, 'showPending'])->name('pending.reports');

    // ── Harassment Reports (assigned to this mentor) ──────────────────────────

    Route::get('/harassment-reports',               [ReportController::class, 'harassmentReports'])->name('harassment.index');
    Route::get('/harassment-analytics',             [ReportController::class, 'harassmentAnalytics'])->name('harassment.analytics');
    Route::get('/harassment-reports/{id}',          [ReportController::class, 'showHarassmentReport'])->name('harassment.show');
    Route::post('/harassment-reports/{id}/respond', [ReportController::class, 'respondToHarassmentReport'])->name('harassment.respond');

    // ── Chat ──────────────────────────────────────────────────────────────────

    Route::get('/chat',                           [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/session/{session}',         [ChatController::class, 'openSessionConversation'])->name('chat.session');
    Route::get('/chat/{conversation}',            [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/end-session', [ChatController::class, 'endSession'])->name('chat.end-session');
    Route::post('/chat/{conversation}/messages',  [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/harassment-reports/{report}/chat', [ChatController::class, 'openHarassmentReportChat'])->name('harassment.chat');
    Route::get('/groups', [MentorSecurityController::class, 'showChatGroups'])->name('groups');
    Route::get('/group',  [MentorSecurityController::class, 'showGroupForm'])->name('group');

    // ── Profile ───────────────────────────────────────────────────────────────

    Route::get('/profile', [MentorSecurityController::class, 'showMyProfile'])->name('profile');

    // ── Guidance Content ──────────────────────────────────────────────────────

    Route::get('/guidance', [GuidanceContentController::class, 'hub'])->name('Guidance');

    Route::get('/guidance/hygiene',          [GuidanceContentController::class, 'hygiene'])->name('hygiene');
    Route::get('/guidance/hygiene/create',   [GuidanceContentController::class, 'createHygiene'])->name('hygiene.create');
    Route::post('/guidance/hygiene',         [GuidanceContentController::class, 'storeHygiene'])->name('hygiene.store');
    Route::get('/guidance/hygiene/{id}/edit', [GuidanceContentController::class, 'editHygiene'])->name('hygiene.edit');
    Route::put('/guidance/hygiene/{id}',     [GuidanceContentController::class, 'updateHygiene'])->name('hygiene.update');

    Route::get('/guidance/general',          [GuidanceContentController::class, 'general'])->name('general');
    Route::get('/guidance/general/create',   [GuidanceContentController::class, 'createGeneral'])->name('general.create');
    Route::post('/guidance/general',         [GuidanceContentController::class, 'storeGeneral'])->name('general.store');
    Route::get('/guidance/general/{id}/edit', [GuidanceContentController::class, 'editGeneral'])->name('general.edit');
    Route::put('/guidance/general/{id}',     [GuidanceContentController::class, 'updateGeneral'])->name('general.update');

    Route::delete('/guidance/content/{id}',          [GuidanceContentController::class, 'destroy'])->name('content.destroy');
    Route::patch('/guidance/content/{id}/publish',   [GuidanceContentController::class, 'publish'])->name('content.publish');
    Route::patch('/guidance/content/{id}/unpublish', [GuidanceContentController::class, 'unpublish'])->name('content.unpublish');

    Route::get('/guidance/emergency', [MentorSecurityController::class, 'showEmergency'])->name('emergency');

    // ── Settings ──────────────────────────────────────────────────────────────

    Route::get('/settings',           [MentorSecurityController::class, 'showSettings'])->name('settings');
    Route::get('/settings/profile',   [MentorSecurityController::class, 'showProfile'])->name('showProfile');
    Route::put('/settings/profile',   [MentorSecurityController::class, 'updateProfile'])->name('updateProfile');
    Route::get('/settings/security',  [MentorSecurityController::class, 'showSecurity'])->name('showSecurity');
    Route::put('/settings/security',  [MentorSecurityController::class, 'updateSecurity'])->name('updateSecurity');
});


// ============================================
// FALLBACK & DEBUG
// ============================================

Route::fallback(fn() => response()->view('errors.404', [], 404));

// Debugging route for mentor available_days casting issue
Route::get('/debug-mentor', function () {
    $mentor = \App\Models\User::find(4);
    return [
        'raw'  => $mentor->getRawOriginal('available_days'),
        'cast' => $mentor->available_days,
    ];
});