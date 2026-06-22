<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportsIssues;
use App\Models\HarassmentReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function mentorUser()
    {
        return Auth::guard('mentor')->user();
    }

    private function mentorCommon(): array
    {
        $mentor = $this->mentorUser();
        $notifications     = $mentor?->notifications()->latest()->get() ?? collect();
        $unreadNotifications = $mentor?->unreadNotifications()->paginate(3) ?? collect();
        $unreadCount       = $notifications->whereNull('read_at')->count();

        return [
            'mentorName'          => $mentor?->name ?? 'Mentor',
            'mentorEmail'         => $mentor?->email ?? '',
            'notifications'       => $notifications,
            'unreadNotifications' => $unreadNotifications,
            'unreadCount'         => $unreadCount,
        ];
    }

    public function showReports()
    {
        return view('mentor.report.index', $this->mentorCommon());
    }

    public function showPending()
    {
        $reports = ReportsIssues::orderBy('created_at', 'desc')->paginate(10);

        return view('mentor.report.pending', array_merge($this->mentorCommon(), compact('reports')));
    }

    public function SubmitReport(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:255',
            'title'       => 'required|string|min:15|max:255',
            'type'        => 'required|in:bug,feedback,request,other',
            'description' => 'required|string|min:200',
            'issue_date'  => 'required|date',
        ], [
            'title.required'       => 'Please provide a title for your issue.',
            'title.min'            => 'The issue title must be at least 15 characters long.',
            'description.required' => 'A detailed description is required.',
            'description.min'      => 'The description must be at least 200 characters long.',
            'type.required'        => 'Please select an issue type.',
            'type.in'              => 'Invalid issue type selected.',
            'issue_date.required'  => 'Please specify when the issue occurred.',
            'issue_date.date'      => 'The issue date must be a valid date/time.',
        ]);

        ReportsIssues::create([
            'username'    => $validated['name'] ?? 'mentor',
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'issue_date'  => $validated['issue_date'],
            'user_id'     => $this->mentorUser()?->id,
        ]);

        return redirect()->route('mentor.pending.reports')->with('success', 'Report submitted successfully!');
    }

    // ── Harassment Reports ────────────────────────────────────────────────────

    public function harassmentReports()
    {
        $mentorId = $this->mentorUser()?->id;

        $reports = HarassmentReport::where('assigned_mentor_id', $mentorId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total'    => HarassmentReport::where('assigned_mentor_id', $mentorId)->count(),
            'pending'  => HarassmentReport::where('assigned_mentor_id', $mentorId)->whereIn('status', ['assigned', 'reviewing'])->count(),
            'resolved' => HarassmentReport::where('assigned_mentor_id', $mentorId)->where('status', 'resolved')->count(),
        ];

        return view('mentor.harassment.index', array_merge($this->mentorCommon(), compact('reports', 'stats')));
    }

    /**
     * Show analytics (reports by incident type) for the mentor
     */
    public function harassmentAnalytics(Request $request)
    {
        $mentorId = $this->mentorUser()?->id;

        // Date range handling: allow presets (7,30,90) or explicit start_date/end_date
        $start = null;
        $end = now();

        if ($request->has('preset')) {
            $preset = (int) $request->preset;
            if (in_array($preset, [7,30,90])) {
                $start = now()->subDays($preset);
            }
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $start = \Carbon\Carbon::parse($request->start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            } catch (\Exception $e) {
                // ignore parse errors and fallback
            }
        }

        $query = HarassmentReport::select('incident_type', DB::raw('count(*) as count'))
            ->where('assigned_mentor_id', $mentorId);

        if ($start) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $typeStats = $query->groupBy('incident_type')->get();

        $labels = $typeStats->pluck('incident_type');
        $data = $typeStats->pluck('count')->map(fn($n) => (int) $n);

        $total = $data->sum();
        $percentages = $data->map(fn($n) => $total ? round(($n / $total) * 100, 1) : 0);

        // Also load detailed report rows in the same range for CSV/PDF exports
        $reportQuery = HarassmentReport::where('assigned_mentor_id', $mentorId)->orderBy('created_at', 'desc');
        if ($start) {
            $reportQuery->whereBetween('created_at', [$start, $end]);
        }

        $reportRows = $reportQuery->get(['reference_number', 'incident_type', 'incident_title', 'incident_date', 'status', 'created_at']);

        $range = [
            'preset' => $request->input('preset'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        return view('mentor.harassment.analytics', array_merge($this->mentorCommon(), compact('labels', 'data', 'percentages', 'total', 'range', 'reportRows')));
    }

    public function showHarassmentReport($id)
    {
        $mentorId = $this->mentorUser()?->id;

        $report = HarassmentReport::where('id', $id)
            ->where('assigned_mentor_id', $mentorId)
            ->firstOrFail();

        // Mark as reviewing if still pending/assigned
        if (in_array($report->status, ['pending', 'assigned'])) {
            $report->update(['status' => 'reviewing']);
        }

        return view('mentor.harassment.show', array_merge($this->mentorCommon(), compact('report')));
    }

    public function respondToHarassmentReport(Request $request, $id)
    {
        $mentorId = $this->mentorUser()?->id;

        $report = HarassmentReport::where('id', $id)
            ->where('assigned_mentor_id', $mentorId)
            ->firstOrFail();

        $request->validate([
            'response' => 'required|string|min:10',
            'status'   => 'required|in:reviewing,resolved',
        ]);

        $report->update([
            'admin_response' => $request->response,
            'responded_at'   => now(),
            'status'         => $request->status,
        ]);

        $mentor = $this->mentorUser();
        $report->notifyOwner(
            'mentor_response',
            'Mentor Response Received',
            $mentor
                ? "{$mentor->name} responded to your report {$report->reference_number}."
                : "A mentor responded to your report {$report->reference_number}.",
            [
                'status' => $request->status,
                'response' => $request->response,
                'mentor_name' => $mentor?->name,
            ]
        );

        return redirect()
            ->route('mentor.harassment.show', $id)
            ->with('success', 'Your response has been saved and the user has been notified.');
    }
}
