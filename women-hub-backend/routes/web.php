<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MentorController;
use App\Http\Controllers\Admin\HarassmentReportController;

// Redirect root to login
// Route::get('/', fn() => redirect()->route('admin.login'));

//home page
Route::get('/', fn() => view('welcome'))->name('welcome');

// get started route
Route::get('/get-started', function() {
    return view('get-started');
})->name('get.started');

// Auth routes (guest only)
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
