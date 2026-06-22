<?php

namespace App\Http\Controllers\Mentor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Log};
use App\Models\{MentorshipSession, ReportsIssues, HarassmentReport};
use App\Events\NewChatRequest;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request){

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';

        $pendingChats = MentorshipSession::where('mentor_id', auth()->id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        $activeChats = MentorshipSession::where('mentor_id', auth()->id())
            ->where('status', 'accepted')
            ->latest()
            ->count();

        if ($request->ajax()) {
            return view('mentor.partials.pending-chats', compact('pendingChats'));
        }

        // notifications
        $notifications = auth()->user()->notifications()->latest()->get();
        $unreadCount = $notifications->where('read_at', null)->count();
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(3);

        // prefer harassment reports assigned to this mentor grouped by incident_type
        $mentorId = $mentorUser?->id;
        $harassmentCounts = HarassmentReport::where('assigned_mentor_id', $mentorId)
            ->selectRaw('incident_type, COUNT(*) as total')
            ->groupBy('incident_type')
            ->pluck('total','incident_type');

        // fallback to general ReportsIssues if no harassment data
        if ($harassmentCounts->isNotEmpty()) {
            $reportCounts = $harassmentCounts;
        } else {
            $reportCounts = ReportsIssues::selectRaw('type, COUNT(*) as total')
                ->groupBy('type')
                ->pluck('total','type');
        }

        return view('mentor.dashboard.index', compact(
            'mentorName',
            'mentorEmail',
            'pendingChats',
            'activeChats',
            'notifications',
            'unreadCount',
            'unreadNotifications',
            'reportCounts'
        ));

    }

}
