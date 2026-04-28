<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportsIssues;
use Illuminate\Support\Facades\Auth;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportController extends Controller
{

        // reports controller
       public function showReports(){

        // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $unreadCount = $notifications->where('read_at', null)->count();

        return view('mentor.report.index', compact(
            'mentorName',
            'mentorEmail',
            'unreadCount',
            'notifications',
            'unreadCount',
            'unreadNotifications'
        ));


    }
       public function showPending(){

        // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';
        $unreadCount = $notifications->where('read_at', null)->count();

        //  Fetch all reports, newest first
        $reports = ReportsIssues::orderBy('created_at', 'desc')->paginate(10);

        return view('mentor.report.pending', compact(
            'mentorName',
            'mentorEmail',
            'unreadCount',
            'notifications',
            'unreadCount',
            'unreadNotifications',
            'reports'
        ));


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
            'user_id'     => auth()->id(),
        ]);

        return redirect()->route('mentor.pending.reports')->with('success', 'Report submitted successfully!');
    }

}
