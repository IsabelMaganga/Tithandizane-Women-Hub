<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expertise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Harassmentreport;
use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalMentors    = User::where('role', 'mentor')->count();
            $activeMentors   = User::where('role', 'mentor')->where('status', 'active')->count();
            $pendingMentors  = User::where('role', 'mentor')->where('status', 'pending')->count();
            $inactiveMentors = User::where('role', 'mentor')->where('status', 'inactive')->count();

            $stats = [
                'mentors' => $totalMentors,
                'reports' => Harassmentreport::count(),
                'admins'  => Admin::count(),
            ];

            $pendingReports  = Harassmentreport::where('status', 'pending')->count();
            $inReviewReports = Harassmentreport::where('status', 'in_review')->count();
            $resolvedReports = Harassmentreport::where('status', 'resolved')->count();

            $totalUsers       = User::count();
            $totalActiveUsers = User::where('status', 'active')->count();

            $mentorCompletionRate = $totalMentors > 0
                ? round(($activeMentors / $totalMentors) * 100)
                : 0;

            $oneWeekAgo         = Carbon::now()->subWeek();
            $newMentorsThisWeek = User::where('role', 'mentor')
                                      ->where('created_at', '>=', $oneWeekAgo)
                                      ->count();
            $newReportsThisWeek = Harassmentreport::where('created_at', '>=', $oneWeekAgo)->count();

            $lastMonthStart    = Carbon::now()->subDays(60);
            $lastMonthEnd      = Carbon::now()->subDays(30);
            $usersLastMonth    = User::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->count();
            $usersThisMonth    = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
            $userGrowthPercent = $usersLastMonth > 0
                ? round((($usersThisMonth - $usersLastMonth) / $usersLastMonth) * 100)
                : 0;

            $recentReports = Harassmentreport::latest()->take(5)->get();
            $recentMentors = User::where('role', 'mentor')->with('expertises')->latest()->take(5)->get();

            $adminUser  = Auth::guard('admin')->user();
            $adminName  = $adminUser ? $adminUser->name  : 'Admin User';
            $adminEmail = $adminUser ? $adminUser->email : 'admin@tithandizane.org';
            $adminRole  = $adminUser ? ($adminUser->role ?? 'System Administrator') : 'System Administrator';

            $chartData      = $this->getChartData();
            $recentActivity = $this->getRecentActivity();

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
            \Log::error('Dashboard error: ' . $e->getMessage());

            return view('admin.dashboard.dashboard', [
                'stats'                => ['mentors' => 0, 'reports' => 0, 'admins' => 0],
                'recentReports'        => collect([]),
                'recentMentors'        => collect([]),
                'totalMentors'         => 0,
                'activeMentors'        => 0,
                'pendingMentors'       => 0,
                'inactiveMentors'      => 0,
                'pendingReports'       => 0,
                'inReviewReports'      => 0,
                'resolvedReports'      => 0,
                'totalUsers'           => 0,
                'totalActiveUsers'     => 0,
                'mentorCompletionRate' => 0,
                'newMentorsThisWeek'   => 0,
                'newReportsThisWeek'   => 0,
                'userGrowthPercent'    => 0,
                'adminName'            => 'Admin User',
                'adminEmail'           => 'admin@tithandizane.org',
                'adminRole'            => 'System Administrator',
                'chartData'            => [],
                'recentActivity'       => collect([]),
            ]);
        }
    }

    // -------------------------------------------------------------------------
    // API endpoint — mentor stats
    // -------------------------------------------------------------------------
    public function mentorStats()
    {
        try {
            $stats = [
                'total'        => User::where('role', 'mentor')->count(),
                'active'       => User::where('role', 'mentor')->where('status', 'active')->count(),
                'pending'      => User::where('role', 'mentor')->where('status', 'pending')->count(),
                'inactive'     => User::where('role', 'mentor')->where('status', 'inactive')->count(),
                'by_expertise' => $this->getMentorExpertiseStats(),
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            \Log::error('Dashboard@mentorStats: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch mentor stats'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Six-month trend data for charts.
     */
    private function getChartData(): array
    {
        try {
            $months      = [];
            $mentorsData = [];
            $usersData   = [];
            $reportsData = [];

            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $start = $month->copy()->startOfMonth();
                $end   = $month->copy()->endOfMonth();

                $months[]      = $month->format('M Y');
                $mentorsData[] = User::where('role', 'mentor')
                                     ->whereBetween('created_at', [$start, $end])
                                     ->count();
                $usersData[]   = User::whereBetween('created_at', [$start, $end])->count();
                $reportsData[] = Harassmentreport::whereBetween('created_at', [$start, $end])->count();
            }

            return [
                'months'  => $months,
                'mentors' => $mentorsData,
                'users'   => $usersData,
                'reports' => $reportsData,
            ];

        } catch (\Exception $e) {
            \Log::error('Chart data error: ' . $e->getMessage());
            return ['months' => [], 'mentors' => [], 'users' => [], 'reports' => []];
        }
    }

    /**
     * Recent activity feed (mentors + reports + users).
     */
    private function getRecentActivity()
    {
        try {
            $recentMentors = User::where('role', 'mentor')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($mentor) => (object)[
                    'type'        => 'mentor_created',
                    'title'       => 'New Mentor Added',
                    'description' => "{$mentor->name} joined as a mentor",
                    'time'        => $mentor->created_at,
                    'icon'        => 'chalkboard-user',
                    'color'       => 'green',
                ]);

            $recentReports = Harassmentreport::latest()
                ->take(3)
                ->get()
                ->map(fn($report) => (object)[
                    'type'        => 'report_submitted',
                    'title'       => 'New Harassment Report',
                    'description' => "Report #{$report->id} requires attention",
                    'time'        => $report->created_at,
                    'icon'        => 'flag',
                    'color'       => 'red',
                ]);

            // Excludes mentors — adjust 'user' to match your actual role name
            $recentUsers = User::where('role', 'user')
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($user) => (object)[
                    'type'        => 'user_registered',
                    'title'       => 'New User Registration',
                    'description' => "{$user->name} joined the platform",
                    'time'        => $user->created_at,
                    'icon'        => 'user-plus',
                    'color'       => 'blue',
                ]);

            return $recentMentors
                ->concat($recentReports)
                ->concat($recentUsers)
                ->sortByDesc('time')
                ->take(10);

        } catch (\Exception $e) {
            \Log::error('Recent activity error: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * Top 5 expertise areas via the pivot table (expertise_user).
     * Replaces the old JSON-column approach.
     */
    private function getMentorExpertiseStats(): array
    {
        try {
            return Expertise::withCount([
                    // Only count mentors that are active
                    'mentors as mentors_count' => fn($q) => $q->where('status', 'active'),
                ])
                ->having('mentors_count', '>', 0)
                ->orderByDesc('mentors_count')
                ->take(5)
                ->get()
                ->pluck('mentors_count', 'name')
                ->toArray();

        } catch (\Exception $e) {
            \Log::error('Expertise stats error: ' . $e->getMessage());
            return [];
        }
    }
}