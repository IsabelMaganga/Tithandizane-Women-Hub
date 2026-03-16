<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'mentors' => \App\Models\Mentor::count(),
            'reports' => \App\Models\Harassmentreport::count(),
            'admins' => \App\Models\Admin::count(),
        ];

        // Get recent data
        $recentReports = \App\Models\Harassmentreport::latest()->take(5)->get();
        $recentMentors = \App\Models\Mentor::latest()->take(5)->get();

        // Additional statistics for new dashboard
        $totalMentors = $stats['mentors'];
        $activeMentors = \App\Models\Mentor::where('status', 'active')->count();
        $pendingReports = \App\Models\Harassmentreport::where('status', 'pending')->count();
        $inReviewReports = \App\Models\Harassmentreport::where('status', 'in_review')->count();
        $totalUsers = \App\Models\User::count() + $totalMentors; // Users + mentors
        
        // Calculate completion rate
        $mentorCompletionRate = $totalMentors > 0 ? round(($activeMentors / $totalMentors) * 100) : 0;
        
        // New mentors this week (simplified - using all mentors as "new")
        $newMentorsThisWeek = min(3, $totalMentors); // Placeholder logic

        // Get current admin user info
        $adminUser = Auth::guard('admin')->user();
        $adminName = $adminUser ? $adminUser->name : 'Admin';
        $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.com';

        return view('admin.dashboard', compact(
            'stats', 
            'recentReports', 
            'recentMentors',
            'totalMentors',
            'activeMentors', 
            'pendingReports',
            'inReviewReports',
            'totalUsers',
            'mentorCompletionRate',
            'newMentorsThisWeek',
            'adminName',
            'adminEmail'
        ));
    }
}
