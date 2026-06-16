<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HarassmentReport;
use App\Models\ReportsIssues;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{

        // reports controller
       public function showReports()
    {
        $context = $this->mentorContext();
        return view('mentor.report.index', $context);
    }

    public function showPending()
    {
        $context = $this->mentorContext();

        //  Fetch all reports, newest first
        $reports = ReportsIssues::orderBy('created_at', 'desc')->paginate(10);

        return view('mentor.report.pending', array_merge($context, compact('reports')));
    }

    public function assignedReports()
    {
        $context = $this->mentorContext();

        $reports = HarassmentReport::assignedToMentor(Auth::guard('mentor')->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('mentor.report.assigned', array_merge($context, compact('reports')));
    }

    public function showAssignedReport($id)
    {
        $context = $this->mentorContext();

        $report = HarassmentReport::where('assigned_mentor_id', Auth::guard('mentor')->id())
            ->findOrFail($id);

        return view('mentor.report.assigned-show', array_merge($context, compact('report')));
    }

    private function mentorContext(): array
    {
        $mentor = Auth::guard('mentor')->user();
        $notifications = $mentor ? $mentor->notifications()->latest()->get() : collect();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = $mentor ? $mentor->unreadNotifications()->paginate(3) : collect();

        return [
            'mentorName' => $mentor?->name ?? 'Mentor',
            'mentorEmail' => $mentor?->email ?? 'mentor@tithandizane.com',
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
        ];
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


        // Mass assignment
        ReportsIssues::create([
            'username'    => $validated['name'] ?? 'mentor',
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'issue_date'  => $validated['issue_date'],
            'user_id'     => Auth::guard('mentor')->id(),
        ]);

        return redirect()->route('mentor.pending.reports')->with('success', 'Report submitted successfully!');
    }

}
