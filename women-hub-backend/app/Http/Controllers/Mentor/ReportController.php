<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportsIssues;
use App\Models\HarassmentReport;
use Illuminate\Support\Facades\Auth;

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
