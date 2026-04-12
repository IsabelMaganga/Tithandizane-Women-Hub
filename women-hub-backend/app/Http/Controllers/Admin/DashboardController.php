<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mentor;
use App\Models\Harassmentreport;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Get dashboard statistics with error handling
            $stats = [
                'mentors' => Mentor::count(),
                'reports' => Harassmentreport::count(),
                'admins' => Admin::count(),
            ];

            // Get recent data
            $recentReports = Harassmentreport::latest()->take(5)->get();
            $recentMentors = Mentor::latest()->take(5)->get();

            // Additional statistics for new dashboard
            $totalMentors = $stats['mentors'];
            $activeMentors = Mentor::where('status', 'active')->count();
            $pendingMentors = Mentor::where('status', 'pending')->count();
            $inactiveMentors = Mentor::where('status', 'inactive')->count();
            
            // Report statistics
            $pendingReports = Harassmentreport::where('status', 'pending')->count();
            $inReviewReports = Harassmentreport::where('status', 'in_review')->count();
            $resolvedReports = Harassmentreport::where('status', 'resolved')->count();
            
            // User statistics
            $totalUsers = User::count();
            $totalActiveUsers = User::where('status', 'active')->count();
            
            // Calculate mentor completion rate
            $mentorCompletionRate = $totalMentors > 0 ? round(($activeMentors / $totalMentors) * 100) : 0;

            // New mentors this week
            $oneWeekAgo = Carbon::now()->subWeek();
            $newMentorsThisWeek = Mentor::where('created_at', '>=', $oneWeekAgo)->count();
            
            // New reports this week
            $newReportsThisWeek = Harassmentreport::where('created_at', '>=', $oneWeekAgo)->count();
            
            // User growth percentage (mock calculation - compare with last month)
            $lastMonth = Carbon::now()->subMonth();
            $usersLastMonth = User::where('created_at', '<', Carbon::now())->where('created_at', '>=', $lastMonth)->count();
            $userGrowthPercent = $usersLastMonth > 0 ? round((($totalUsers - $usersLastMonth) / $usersLastMonth) * 100) : 0;

            // Get current admin user info
            $adminUser = Auth::guard('admin')->user();
            $adminName = $adminUser ? $adminUser->name : 'Admin User';
            $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
            $adminRole = $adminUser ? ($adminUser->role ?? 'System Administrator') : 'System Administrator';

            // Prepare chart data for analytics
            $chartData = $this->getChartData();
            
            // Prepare recent activity data
            $recentActivity = $this->getRecentActivity();

            // Updated view path from 'dashboard' to 'admin.dashboard.dashboard'
            return view('admin.dashboard.dashboard', compact(
                'stats',
                'recentReports',
                'recentMentors',
                'totalMentors',
                'activeMentors',
                'pendingMentors',
                'inactiveMentors',
                'pendingReports',
                'inReviewReports',
                'resolvedReports',
                'totalUsers',
                'totalActiveUsers',
                'mentorCompletionRate',
                'newMentorsThisWeek',
                'newReportsThisWeek',
                'userGrowthPercent',
                'adminName',
                'adminEmail',
                'adminRole',
                'chartData',
                'recentActivity'
            ));
            
        } catch (\Exception $e) {
            // Handle errors gracefully
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Updated view path from 'dashboard' to 'admin.dashboard.dashboard'
            return view('admin.dashboard.dashboard', [
                'stats' => ['mentors' => 0, 'reports' => 0, 'admins' => 0],
                'recentReports' => collect([]),
                'recentMentors' => collect([]),
                'totalMentors' => 0,
                'activeMentors' => 0,
                'pendingMentors' => 0,
                'inactiveMentors' => 0,
                'pendingReports' => 0,
                'inReviewReports' => 0,
                'resolvedReports' => 0,
                'totalUsers' => 0,
                'totalActiveUsers' => 0,
                'mentorCompletionRate' => 0,
                'newMentorsThisWeek' => 0,
                'newReportsThisWeek' => 0,
                'userGrowthPercent' => 0,
                'adminName' => 'Admin User',
                'adminEmail' => 'admin@tithandizane.org',
                'adminRole' => 'System Administrator',
                'chartData' => [],
                'recentActivity' => collect([])
            ]);
        }
    }

    /**
     * Get chart data for analytics
     */
    private function getChartData()
    {
        try {
            // Get last 6 months of data
            $months = [];
            $mentorsData = [];
            $usersData = [];
            $reportsData = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $months[] = $month->format('M Y');
                
                $startOfMonth = $month->copy()->startOfMonth();
                $endOfMonth = $month->copy()->endOfMonth();
                
                $mentorsData[] = Mentor::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
                $usersData[] = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
                $reportsData[] = Harassmentreport::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            }

            return [
                'months' => $months,
                'mentors' => $mentorsData,
                'users' => $usersData,
                'reports' => $reportsData
            ];
            
        } catch (\Exception $e) {
            \Log::error('Chart data error: ' . $e->getMessage());
            return [
                'months' => [],
                'mentors' => [],
                'users' => [],
                'reports' => []
            ];
        }
    }

    /**
     * Get recent activity across the platform
     */
    private function getRecentActivity()
    {
        try {
            $activity = collect();
            
            // Get recent mentor creations
            $recentMentors = Mentor::latest()->take(3)->get()->map(function($mentor) {
                return (object)[
                    'type' => 'mentor_created',
                    'title' => 'New Mentor Added',
                    'description' => "{$mentor->name} joined as a mentor",
                    'time' => $mentor->created_at,
                    'icon' => 'chalkboard-user',
                    'color' => 'green'
                ];
            });
            
            // Get recent reports
            $recentReports = Harassmentreport::latest()->take(3)->get()->map(function($report) {
                return (object)[
                    'type' => 'report_submitted',
                    'title' => 'New Harassment Report',
                    'description' => "Report #{$report->id} requires attention",
                    'time' => $report->created_at,
                    'icon' => 'flag',
                    'color' => 'red'
                ];
            });
            
            // Get recent user registrations
            $recentUsers = User::latest()->take(3)->get()->map(function($user) {
                return (object)[
                    'type' => 'user_registered',
                    'title' => 'New User Registration',
                    'description' => "{$user->name} joined the platform",
                    'time' => $user->created_at,
                    'icon' => 'user-plus',
                    'color' => 'blue'
                ];
            });
            
            // Merge and sort all activity
            $activity = $recentMentors->concat($recentReports)->concat($recentUsers);
            $activity = $activity->sortByDesc('time')->take(10);
            
            return $activity;
            
        } catch (\Exception $e) {
            \Log::error('Recent activity error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Get mentor statistics for API endpoint (if needed)
     */
    public function mentorStats()
    {
        try {
            $stats = [
                'total' => Mentor::count(),
                'active' => Mentor::where('status', 'active')->count(),
                'pending' => Mentor::where('status', 'pending')->count(),
                'inactive' => Mentor::where('status', 'inactive')->count(),
                'by_expertise' => $this->getMentorExpertiseStats(),
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch mentor stats'], 500);
        }
    }

    /**
     * Get mentor expertise statistics
     */
    private function getMentorExpertiseStats()
    {
        try {
            $mentors = Mentor::all();
            $expertiseCount = [];
            
            foreach ($mentors as $mentor) {
                $expertise = json_decode($mentor->expertise, true);
                if (is_array($expertise)) {
                    foreach ($expertise as $area) {
                        $expertiseCount[$area] = ($expertiseCount[$area] ?? 0) + 1;
                    }
                }
            }
            
            arsort($expertiseCount);
            return array_slice($expertiseCount, 0, 5); // Top 5 expertise areas
            
        } catch (\Exception $e) {
            \Log::error('Expertise stats error: ' . $e->getMessage());
            return [];
        }
    }
}