<?php

namespace App\Http\Controllers\Mentor;

use App\Events\NewChatRequest;
use App\Http\Controllers\Controller;
use App\Models\MentorshipSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        return view('mentor.dashboard.index', compact(
            'mentorName',
            'mentorEmail',
            'pendingChats',
            'activeChats'
        ));

    }

}
