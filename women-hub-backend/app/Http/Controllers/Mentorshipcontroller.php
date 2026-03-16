<?php

namespace App\Http\Controllers;

use App\Models\MentorshipSession;
use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\Request;

class MentorshipController extends Controller
{
    public function mentors(Request $request)
    {
        $mentors = User::where('role', 'mentor')
            ->where('is_available', true)
            ->select('id', 'name', 'bio', 'expertise_area', 'available_days', 'available_time_start', 'available_time_end')
            ->get();

        return response()->json($mentors);
    }

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

    public function mySessions(Request $request)
    {
        $sessions = MentorshipSession::where('mentee_id', $request->user()->id)
            ->with('mentor:id,name,expertise_area,bio')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sessions);
    }

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

        // Auto-create conversation when the session is accepted
        if ($validated['status'] === 'accepted') {
            $existingConvo = Conversation::where('is_group', false)
                ->whereHas('participants', function($q) use ($session) {
                    $q->where('user_id', $session->mentor_id);
                })
                ->whereHas('participants', function($q) use ($session) {
                    $q->where('user_id', $session->mentee_id);
                })->first();

            if (!$existingConvo) {
                $conversation = Conversation::create([
                    'is_group' => false,
                    'name' => null
                ]);
                $conversation->participants()->attach([$session->mentor_id, $session->mentee_id]);
            }
        }

        return response()->json([
            'message' => 'Session updated',
            'session' => $session,
        ]);
    }
}