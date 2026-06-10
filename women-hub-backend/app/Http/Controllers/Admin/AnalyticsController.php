<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HarassmentReport;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            // Get date range filters
            $startDate = $request->get('start', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end', now()->format('Y-m-d'));
            
            // Base query with date filter
            $reportsQuery = HarassmentReport::whereBetween('created_at', [$startDate, $endDate]);
            
            // ========== KPI Calculations ==========
            
            // Total Reports
            $totalReports = HarassmentReport::count();
            
            // Reports in selected period
            $reportsInPeriod = $reportsQuery->count();
            
            // Previous period for growth calculation
            $previousPeriodStart = now()->subDays(60);
            $previousPeriodEnd = now()->subDays(31);
            $previousPeriodReports = HarassmentReport::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])->count();
            
            // Report growth percentage
            $reportGrowth = $previousPeriodReports > 0 
                ? round((($reportsInPeriod - $previousPeriodReports) / $previousPeriodReports) * 100, 1)
                : 0;
            
            // Resolution Rate
            $resolvedReports = HarassmentReport::where('status', 'resolved')->count();
            $resolutionRate = $totalReports > 0 ? round(($resolvedReports / $totalReports) * 100, 1) : 0;
            
            // Active Mentors (mentors who have been assigned at least one report)
            $activeMentors = User::whereHas('assignedReports')->count();
            
            // Assigned reports count
            $assignedReports = HarassmentReport::whereIn('status', ['assigned', 'reviewing'])->count();
            
            // Average Response Time (time from report creation to first admin response)
            $avgResponseTime = HarassmentReport::whereNotNull('responded_at')
                ->select(DB::raw('AVG(JULIANDAY(responded_at) - JULIANDAY(created_at)) * 24 as avg_hours'))
                ->value('avg_hours');
            $avgResponseTime = $avgResponseTime ? round($avgResponseTime, 1) : 0;
            
            // Response time trend (comparing last 30 days vs previous 30 days)
            $recentAvgResponse = HarassmentReport::whereNotNull('responded_at')
                ->whereBetween('created_at', [now()->subDays(30), now()])
                ->select(DB::raw('AVG(JULIANDAY(responded_at) - JULIANDAY(created_at)) * 24 as avg_hours'))
                ->value('avg_hours');
            
            $previousAvgResponse = HarassmentReport::whereNotNull('responded_at')
                ->whereBetween('created_at', [now()->subDays(60), now()->subDays(31)])
                ->select(DB::raw('AVG(JULIANDAY(responded_at) - JULIANDAY(created_at)) * 24 as avg_hours'))
                ->value('avg_hours');
            
            $responseTimeTrend = ($previousAvgResponse && $previousAvgResponse > 0) 
                ? round((($recentAvgResponse - $previousAvgResponse) / $previousAvgResponse) * 100, 1)
                : 0;
            
            // ========== Chart Data ==========
            
            // Reports Trend (Last 6 months)
            $trendData = [];
            $trendLabels = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $trendLabels[] = $month->format('M Y');
                $count = HarassmentReport::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count();
                $trendData[] = $count;
            }
            
            // Reports by Type
            $typeStats = HarassmentReport::select('incident_type', DB::raw('count(*) as count'))
                ->groupBy('incident_type')
                ->get();
            
            $typeLabels = $typeStats->pluck('incident_type')->map(function($type) {
                return ucfirst($type);
            })->toArray();
            $typeData = $typeStats->pluck('count')->toArray();
            
            // If no data, provide defaults
            if (empty($typeLabels)) {
                $typeLabels = ['Physical', 'Verbal', 'Sexual', 'Cyber', 'Other'];
                $typeData = [0, 0, 0, 0, 0];
            }
            
            // Status Distribution
            $statusStats = HarassmentReport::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();
            
            $statusLabels = $statusStats->pluck('status')->map(function($status) {
                return ucfirst($status);
            })->toArray();
            $statusData = $statusStats->pluck('count')->toArray();
            
            // Severity Breakdown
            $severityStats = HarassmentReport::select('severity', DB::raw('count(*) as count'))
                ->whereNotNull('severity')
                ->groupBy('severity')
                ->get();
            
            $severityData = [
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ];
            
            foreach ($severityStats as $stat) {
                if (isset($severityData[$stat->severity])) {
                    $severityData[$stat->severity] = $stat->count;
                }
            }
            
            // Mentor Performance (Top 5 mentors by cases handled)
            $mentorPerformance = User::withCount(['assignedReports as cases_count'])
                ->having('cases_count', '>', 0)
                ->orderBy('cases_count', 'desc')
                ->limit(5)
                ->get();
            
            $mentorNames = $mentorPerformance->pluck('name')->toArray();
            $mentorCases = $mentorPerformance->pluck('cases_count')->toArray();
            
            // Anonymous vs Identified
            $anonymousCount = HarassmentReport::where('is_anonymous', true)->count();
            $identifiedCount = HarassmentReport::where('is_anonymous', false)->count();
            $anonymousData = [$anonymousCount, $identifiedCount];
            
            // Top Locations
            $topLocations = HarassmentReport::select('incident_location', DB::raw('count(*) as count'))
                ->whereNotNull('incident_location')
                ->where('incident_location', '!=', '')
                ->groupBy('incident_location')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get();
            
            // Weekly Pattern
            $weeklyData = [];
            for ($i = 1; $i <= 7; $i++) {
                $count = HarassmentReport::whereRaw('strftime("%w", created_at) = ?', [$i % 7])->count();
                $weeklyData[] = $count;
            }
            
            // ========== Insights and Recommendations ==========
            
            // Generate dynamic insights
            $mostCommonType = $typeStats->sortByDesc('count')->first();
            $reportsTrend = $trendData[5] > $trendData[0] 
                ? "Reports have increased by " . round((($trendData[5] - $trendData[0]) / max($trendData[0], 1)) * 100) . "% in the last 6 months."
                : "Reports have decreased or remained stable in the last 6 months.";
            
            $alertMessage = "";
            if ($mostCommonType && $mostCommonType->incident_type === 'sexual') {
                $alertMessage = "Sexual harassment reports are the most common type (" . $mostCommonType->count . " reports), requiring immediate attention and specialized mentoring.";
            } elseif ($mostCommonType && $mostCommonType->incident_type === 'physical') {
                $alertMessage = "Physical harassment reports are prevalent. Consider adding self-defense workshops and safety resources.";
            } else {
                $alertMessage = ucfirst($mostCommonType->incident_type ?? 'Harassment') . " reports are the most common type, requiring focused intervention strategies.";
            }
            
            // Recommendation based on data
            $pendingCount = HarassmentReport::where('status', 'pending')->count();
            $highSeverityCount = HarassmentReport::where('severity', 'high')->where('status', '!=', 'resolved')->count();
            
            $recommendation = "";
            if ($pendingCount > 10) {
                $recommendation = "There are {$pendingCount} pending reports. Consider adding more mentors to reduce backlog.";
            } elseif ($highSeverityCount > 5) {
                $recommendation = "High severity reports ({$highSeverityCount}) need immediate attention. Prioritize these cases.";
            } else {
                $recommendation = "Current performance is good. Continue monitoring trends and provide regular training to mentors.";
            }
            
            // ========== Prepare View Data ==========
            $viewData = [
                // KPI Data
                'totalReports' => $totalReports,
                'reportGrowth' => $reportGrowth,
                'resolutionRate' => $resolutionRate,
                'resolvedReports' => $resolvedReports,
                'activeMentors' => $activeMentors,
                'assignedReports' => $assignedReports,
                'avgResponseTime' => $avgResponseTime,
                'responseTimeTrend' => $responseTimeTrend,
                
                // Chart Data
                'trendLabels' => $trendLabels,
                'trendData' => $trendData,
                'typeLabels' => $typeLabels,
                'typeData' => $typeData,
                'statusLabels' => $statusLabels,
                'statusData' => $statusData,
                'severityData' => array_values($severityData),
                'mentorNames' => $mentorNames,
                'mentorCases' => $mentorCases,
                'anonymousData' => $anonymousData,
                'topLocations' => $topLocations,
                'weeklyData' => $weeklyData,
                
                // Insights
                'reportsTrend' => $reportsTrend,
                'alertMessage' => $alertMessage,
                'recommendation' => $recommendation,
            ];
            
            return view('admin.analytics.index', $viewData);
            
        } catch (\Exception $e) {
            Log::error('Analytics error: ' . $e->getMessage());
            
            // Return view with default/empty data
            return view('admin.analytics.index', $this->getDefaultAnalyticsData());
        }
    }
    
    /**
     * Export analytics as PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Load the same data as the index method
            $data = $this->getAnalyticsData($request);
            
            // You can use a PDF package like barryvdh/laravel-dompdf
            // For now, we'll redirect back with a message
            return redirect()->back()->with('info', 'PDF export feature coming soon. Please check back later.');
            
            // When you install a PDF package, use something like:
            // $pdf = PDF::loadView('admin.analytics.pdf', $data);
            // return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('PDF Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }
    
    /**
     * Export analytics as Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // You can use a package like maatwebsite/excel
            // For now, we'll redirect back with a message
            return redirect()->back()->with('info', 'Excel export feature coming soon. Please check back later.');
            
            // When you install Excel package, use something like:
            // return Excel::download(new AnalyticsExport($request), 'analytics-report.xlsx');
            
        } catch (\Exception $e) {
            Log::error('Excel Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate Excel file. Please try again.');
        }
    }
    
    /**
     * Get analytics data for exports
     */
    private function getAnalyticsData(Request $request)
    {
        $startDate = $request->get('start', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end', now()->format('Y-m-d'));
        
        return [
            'totalReports' => HarassmentReport::count(),
            'reportsByType' => HarassmentReport::select('incident_type', DB::raw('count(*) as count'))
                ->groupBy('incident_type')
                ->get(),
            'reportsByStatus' => HarassmentReport::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'dateRange' => ['start' => $startDate, 'end' => $endDate],
            'generatedAt' => now(),
        ];
    }
    
    /**
     * Get default analytics data when an error occurs
     */
    private function getDefaultAnalyticsData()
    {
        return [
            'totalReports' => 0,
            'reportGrowth' => 0,
            'resolutionRate' => 0,
            'resolvedReports' => 0,
            'activeMentors' => 0,
            'assignedReports' => 0,
            'avgResponseTime' => 0,
            'responseTimeTrend' => 0,
            'trendLabels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'trendData' => [0, 0, 0, 0, 0, 0],
            'typeLabels' => ['Physical', 'Verbal', 'Sexual', 'Cyber', 'Other'],
            'typeData' => [0, 0, 0, 0, 0],
            'statusLabels' => ['Pending', 'Reviewing', 'Assigned', 'Resolved', 'Dismissed'],
            'statusData' => [0, 0, 0, 0, 0],
            'severityData' => [0, 0, 0],
            'mentorNames' => [],
            'mentorCases' => [],
            'anonymousData' => [0, 0],
            'topLocations' => collect([]),
            'weeklyData' => [0, 0, 0, 0, 0, 0, 0],
            'reportsTrend' => 'No data available for the selected period.',
            'alertMessage' => 'Insufficient data to generate alerts.',
            'recommendation' => 'Collect more data to get meaningful recommendations.',
        ];
    }
}