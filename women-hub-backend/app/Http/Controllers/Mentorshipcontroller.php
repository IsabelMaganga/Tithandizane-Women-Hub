<?php

namespace App\Http\Controllers;

use App\Models\MentorshipSession;
use App\Models\User;
use Illuminate\Http\Request;

class MentorshipController extends Controller
{
    // List all available mentors
    public function mentors(Request $request)
    {
        $mentors = User::where('role', 'mentor')
            ->where('is_available', true)
            ->select('id', 'name', 'bio', 'expertise_area', 'available_days', 'available_time_start', 'available_time_end')
            ->get();

        return response()->json($mentors);
    }

    // Request a mentorship session
    public function request(Request $request)
    {
        $validated = $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'topic' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $session = MentorshipSession::create([
            'mentor_id' => $validated['mentor_id'],
            'mentee_id' => $request->user()->id,
            'topic' => $validated['topic'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        $session->load('mentor:id,name,expertise_area');

        return response()->json([
            'message' => 'Mentorship request sent successfully',
            'session' => $session,
        ], 201);
    }

    // Get my mentorship sessions
    public function mySessions(Request $request)
    {
        $sessions = MentorshipSession::where('mentee_id', $request->user()->id)
            ->with('mentor:id,name,expertise_area,bio')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sessions);
    }

    // For mentors: get their sessions
    public function mentorSessions(Request $request)
    {
        if (!$request->user()->isMentor()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sessions = MentorshipSession::where('mentor_id', $request->user()->id)
            ->with('mentee:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sessions);
    }

    // For mentors: update session status
    public function updateStatus(Request $request, MentorshipSession $session)
    {
        if ($session->mentor_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:accepted,declined,completed',
            'mentor_notes' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        $session->update($validated);

        return response()->json([
            'message' => 'Session updated',
            'session' => $session,
        ]);
    }
}