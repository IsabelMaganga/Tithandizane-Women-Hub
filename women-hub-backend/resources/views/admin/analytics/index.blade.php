@extends('admin.layouts.admin')
@section('title', 'Analytics & Reports')
@section('page-title', 'Analytics Dashboard')
@section('page-subtitle', 'Comprehensive insights and statistics for women safety and mentorship programs')

@push('styles')
<style>
    .stat-card {
        background: var(--card-bg);
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid var(--border-color);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -12px rgba(0,0,0,0.1);
    }
    .chart-container {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid var(--border-color);
        margin-bottom: 24px;
    }
    .chart-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text-primary);
        border-left: 4px solid var(--purple);
        padding-left: 12px;
    }
    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
    }
    .kpi-label {
        font-size: 12px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .trend-up { color: var(--teal-green); }
    .trend-down { color: var(--red); }
    .insight-box {
        background: linear-gradient(135deg, var(--light-purple) 0%, var(--light-blue) 100%);
        border-radius: 12px;
        padding: 16px;
        margin-top: 20px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4">

    {{-- Date Range Filter --}}
    <div class="mb-6 flex justify-between items-center flex-wrap gap-4">
        <div class="flex gap-3">
            <button onclick="setDateRange('week')" class="btn-gray px-4 py-2 rounded-lg text-sm">Last 7 Days</button>
            <button onclick="setDateRange('month')" class="btn-gray px-4 py-2 rounded-lg text-sm">Last 30 Days</button>
            <button onclick="setDateRange('year')" class="btn-gray px-4 py-2 rounded-lg text-sm">Last 12 Months</button>
        </div>
        <div class="flex gap-3">
            <input type="date" id="startDate" class="form-input rounded-lg px-3 py-2 text-sm" value="{{ request('start', now()->subDays(30)->format('Y-m-d')) }}">
            <input type="date" id="endDate" class="form-input rounded-lg px-3 py-2 text-sm" value="{{ request('end', now()->format('Y-m-d')) }}">
            <button onclick="applyDateRange()" class="btn-purple px-4 py-2 rounded-lg text-sm">Apply</button>
        </div>
    </div>

    {{-- Key Performance Indicators --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="kpi-label">Total Reports</p>
                    <p class="kpi-value mt-2">{{ number_format($totalReports ?? 0) }}</p>
                    <p class="text-xs mt-2 {{ ($reportGrowth ?? 0) >= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ ($reportGrowth ?? 0) >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($reportGrowth ?? 0) }}% from last period
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <i class="fas fa-flag text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="kpi-label">Resolution Rate</p>
                    <p class="kpi-value mt-2">{{ number_format($resolutionRate ?? 0) }}%</p>
                    <p class="text-xs mt-2 text-success">
                        <i class="fas fa-check-circle"></i> {{ number_format($resolvedReports ?? 0) }} resolved
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $resolutionRate ?? 0 }}%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="kpi-label">Active Mentors</p>
                    <p class="kpi-value mt-2">{{ number_format($activeMentors ?? 0) }}</p>
                    <p class="text-xs mt-2">Assigned to {{ number_format($assignedReports ?? 0) }} cases</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <i class="fas fa-chalkboard-user text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="kpi-label">Avg Response Time</p>
                    <p class="kpi-value mt-2">{{ number_format($avgResponseTime ?? 0) }} hrs</p>
                    <p class="text-xs mt-2 {{ ($responseTimeTrend ?? 0) <= 0 ? 'trend-up' : 'trend-down' }}">
                        <i class="fas fa-arrow-{{ ($responseTimeTrend ?? 0) <= 0 ? 'down' : 'up' }}"></i>
                        {{ abs($responseTimeTrend ?? 0) }}% improvement
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="chart-container">
            <h3 class="chart-title">Reports Trend (Last 6 Months)</h3>
            <canvas id="reportsTrendChart" height="250"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Reports by Incident Type</h3>
            <canvas id="reportsByTypeChart" height="250"></canvas>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="chart-container">
            <h3 class="chart-title">Report Status Distribution</h3>
            <canvas id="statusDistributionChart" height="250"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Severity Breakdown</h3>
            <canvas id="severityChart" height="250"></canvas>
        </div>
    </div>

    {{-- Charts Row 3 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="chart-container">
            <h3 class="chart-title">Top Performing Mentors</h3>
            <canvas id="mentorPerformanceChart" height="250"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Anonymous vs Identified Reports</h3>
            <canvas id="anonymousChart" height="250"></canvas>
        </div>
    </div>

    {{-- Detailed Statistics Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="chart-container">
            <h3 class="chart-title">Reports by Location (Top 10)</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse(($topLocations ?? collect()) as $location)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span>{{ $location->incident_location ?? 'Unknown' }}</span>
                        <span>{{ $location->count ?? 0 }} reports</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $maxCount = $topLocations && $topLocations->count() ? $topLocations->max('count') : 1;
                            $widthPercent = $maxCount > 0 ? (($location->count ?? 0) / $maxCount) * 100 : 0;
                        @endphp
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $widthPercent }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No location data available</p>
                @endforelse
            </div>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Reports by Day of Week</h3>
            <canvas id="weeklyPatternChart" height="250"></canvas>
        </div>
    </div>

    {{-- Insights and Recommendations --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="insight-box">
            <i class="fas fa-chart-line text-2xl mb-3 block"></i>
            <h4 class="font-bold mb-2">Key Insight #1</h4>
            <p class="text-sm opacity-90">{{ $reportsTrend ?? 'Reports have increased by 15% in the last quarter.' }}</p>
        </div>
        <div class="insight-box">
            <i class="fas fa-exclamation-triangle text-2xl mb-3 block"></i>
            <h4 class="font-bold mb-2">Alert</h4>
            <p class="text-sm opacity-90">{{ $alertMessage ?? 'Sexual harassment reports are the most common type.' }}</p>
        </div>
        <div class="insight-box">
            <i class="fas fa-lightbulb text-2xl mb-3 block"></i>
            <h4 class="font-bold mb-2">Recommendation</h4>
            <p class="text-sm opacity-90">{{ $recommendation ?? 'Increase awareness campaigns in high-reporting locations.' }}</p>
        </div>
    </div>

    {{-- Export Options --}}
    <div class="flex justify-end gap-3 mb-8">
        <button onclick="exportReport('pdf')" class="btn-purple px-5 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-file-pdf"></i> Export as PDF
        </button>
        <button onclick="exportReport('excel')" class="btn-gray px-5 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-file-excel"></i> Export as Excel
        </button>
        <button onclick="window.print()" class="btn-gray px-5 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-print"></i> Print
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Store PHP data in JavaScript variables at the top
var trendLabels = <?php echo json_encode($trendLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>;
var trendData = <?php echo json_encode($trendData ?? [12, 19, 15, 17, 14, 23]); ?>;
var typeLabels = <?php echo json_encode($typeLabels ?? ['Physical', 'Verbal', 'Sexual', 'Cyber', 'Other']); ?>;
var typeData = <?php echo json_encode($typeData ?? [15, 25, 30, 20, 10]); ?>;
var statusLabels = <?php echo json_encode($statusLabels ?? ['Pending', 'Reviewing', 'Assigned', 'Resolved', 'Dismissed']); ?>;
var statusData = <?php echo json_encode($statusData ?? [10, 8, 12, 25, 5]); ?>;
var severityData = <?php echo json_encode($severityData ?? [20, 45, 35]); ?>;
var mentorNames = <?php echo json_encode($mentorNames ?? ['Sarah M.', 'John D.', 'Lisa K.', 'David W.', 'Anna P.']); ?>;
var mentorCases = <?php echo json_encode($mentorCases ?? [12, 10, 8, 7, 5]); ?>;
var anonymousData = <?php echo json_encode($anonymousData ?? [30, 70]); ?>;
var weeklyData = <?php echo json_encode($weeklyData ?? [8, 10, 12, 15, 18, 20, 14]); ?>;

let reportsTrendChart, reportsByTypeChart, statusDistributionChart, severityChart, mentorPerformanceChart, anonymousChart, weeklyPatternChart;

document.addEventListener('DOMContentLoaded', function() {
    // Reports Trend Chart
    const trendCtx = document.getElementById('reportsTrendChart').getContext('2d');
    reportsTrendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Number of Reports',
                data: trendData,
                borderColor: '#9b59b6',
                backgroundColor: 'rgba(155, 89, 182, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
    });

    // Reports by Type Chart
    const typeCtx = document.getElementById('reportsByTypeChart').getContext('2d');
    reportsByTypeChart = new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: ['#9b59b6', '#e74c3c', '#f39c12', '#3498db', '#95a5a6']
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusDistributionChart').getContext('2d');
    statusDistributionChart = new Chart(statusCtx, {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: 'Reports',
                data: statusData,
                backgroundColor: ['#f39c12', '#3498db', '#9b59b6', '#2ecc71', '#e74c3c']
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Severity Chart
    const severityCtx = document.getElementById('severityChart').getContext('2d');
    severityChart = new Chart(severityCtx, {
        type: 'doughnut',
        data: {
            labels: ['High', 'Medium', 'Low'],
            datasets: [{ data: severityData, backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Mentor Performance Chart
    const mentorCtx = document.getElementById('mentorPerformanceChart').getContext('2d');
    mentorPerformanceChart = new Chart(mentorCtx, {
        type: 'bar',
        data: {
            labels: mentorNames,
            datasets: [{ label: 'Cases Handled', data: mentorCases, backgroundColor: '#9b59b6' }]
        },
        options: { indexAxis: 'y', responsive: true, plugins: { legend: { position: 'top' } } }
    });

    // Anonymous vs Identified Chart
    const anonCtx = document.getElementById('anonymousChart').getContext('2d');
    anonymousChart = new Chart(anonCtx, {
        type: 'pie',
        data: {
            labels: ['Anonymous', 'Identified'],
            datasets: [{ data: anonymousData, backgroundColor: ['#95a5a6', '#3498db'] }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    // Weekly Pattern Chart
    const weeklyCtx = document.getElementById('weeklyPatternChart').getContext('2d');
    weeklyPatternChart = new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
            datasets: [{
                label: 'Reports',
                data: weeklyData,
                borderColor: '#e74c3c',
                backgroundColor: 'rgba(231, 76, 60, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });
});

function setDateRange(range) {
    const today = new Date();
    let startDate = new Date();
    switch(range) {
        case 'week': startDate.setDate(today.getDate() - 7); break;
        case 'month': startDate.setDate(today.getDate() - 30); break;
        case 'year': startDate.setFullYear(today.getFullYear() - 1); break;
    }
    document.getElementById('startDate').value = startDate.toISOString().split('T')[0];
    document.getElementById('endDate').value = today.toISOString().split('T')[0];
    applyDateRange();
}

function applyDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    if (startDate && endDate) {
        window.location.href = '{{ route("admin.analytics.index") }}?start=' + startDate + '&end=' + endDate;
    }
}

function exportReport(format) {
    if (format === 'pdf') {
        window.location.href = '{{ route("admin.analytics.export-pdf") }}' + window.location.search;
    } else if (format === 'excel') {
        window.location.href = '{{ route("admin.analytics.export-excel") }}' + window.location.search;
    }
}
</script>
@endpush