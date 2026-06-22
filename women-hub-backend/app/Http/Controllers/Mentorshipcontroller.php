<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\MentorReview;
use App\Models\MentorshipSession;
use App\Models\User;
use App\Notifications\ChatRequestNotification;
use App\Notifications\SessionMissedNotification;
use App\Services\MentorAvailabilityService;
use Illuminate\Http\Request;

class MentorshipController extends Controller
{
    public function __construct(private MentorAvailabilityService $availability) {}

    // ──────────────────────────────────────────────────────────────────────────
// Mentor terminates / ends the active conversation
// ──────────────────────────────────────────────────────────────────────────

public function terminateSession(Request $request, MentorshipSession $session)
{
    $user = $request->user();

    // Only the mentor (or an admin) can terminate
    if ($session->mentor_id !== $user->id && !$user->isAdmin()) {
        return response()->json(['message' => 'Only the mentor can end this session.'], 403);
    }

    if ($session->status !== 'accepted') {
        return response()->json([
            'message' => 'Only an active (accepted) session can be terminated.',
        ], 422);
    }

    if (!$session->conversation_started_at) {
        return response()->json([
            'message' => 'The conversation was never started.',
        ], 422);
    }

    $validated = $request->validate([
        'mentor_notes' => 'nullable|string|max:2000',
    ]);

    $session->update([
        'status'           => 'completed',
        'ended_at'         => now(),
        'mentor_notes'     => $validated['mentor_notes'] ?? $session->mentor_notes,
    ]);

    // Notify the mentee that the session has ended and they can leave a review
    $session->load('mentee');
    $session->mentee?->notify(new \App\Notifications\SessionCompletedNotification($session));

    return response()->json([
        'message' => 'Session marked as completed. The mentee can now leave a review.',
        'session' => $session->fresh(),
    ]);
}

    // ──────────────────────────────────────────────────────────────────────────
    // List active mentors
    // ──────────────────────────────────────────────────────────────────────────

    public function mentors(Request $request)
    {
        $mentors = User::where('role', 'mentor')
            ->where('status', 'active')
            ->select('id', 'name', 'email', 'bio', 'expertise_area',
                     'available_days', 'available_time_from', 'available_time_to', 'photo')
            ->get();

        $transformed = $mentors->map(fn($mentor) => [
            'id'                 => $mentor->id,
            'name'               => $mentor->name,
            'bio'                => $mentor->bio,
            'expertise_area'     => $mentor->area_label ?? $mentor->expertise_area,
            'available_days'     => $mentor->available_days,
            'available_time_start' => $mentor->available_time_from,
            'available_time_end'   => $mentor->available_time_to,
            'photo'              => $mentor->photo ? asset('storage/' . $mentor->photo) : null,
            'is_available'       => $mentor->status === 'active',
            'average_rating'     => round(
                MentorReview::where('mentor_id', $mentor->id)->avg('rating') ?? 0, 1
            ),
        ]);

        return response()->json($transformed);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Request a session (with availability + conflict checks)
    // ──────────────────────────────────────────────────────────────────────────

    public function request(Request $request)
    {
        $validated = $request->validate([
            'mentor_id'         => 'required|exists:users,id',
            'topic'             => 'required|string|max:255',
            'message'           => 'nullable|string|max:1000',
            'requested_date'    => 'required|date|after_or_equal:today',
            'requested_time_from' => 'required|date_format:H:i',
            'requested_time_to'   => 'required|date_format:H:i|after:requested_time_from',
        ]);

        $mentor = User::where('id', $validated['mentor_id'])
            ->where('role', 'mentor')
            ->where('status', 'active')
            ->firstOrFail();

        // Availability + conflict check
        $error = $this->availability->check(
            $mentor,
            $validated['requested_date'],
            $validated['requested_time_from'],
            $validated['requested_time_to']
        );

        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $session = MentorshipSession::create([
            'mentor_id'           => $validated['mentor_id'],
            'mentee_id'           => $request->user()->id,
            'topic'               => $validated['topic'],
            'message'             => $validated['message'] ?? null,
            'status'              => 'pending',
            'requested_date'      => $validated['requested_date'],
            'requested_time_from' => $validated['requested_time_from'],
            'requested_time_to'   => $validated['requested_time_to'],
        ]);

        $session->load('mentor:id,name,expertise_area', 'mentee:id,name');
        $mentor->notify(new ChatRequestNotification($session));

        return response()->json([
            'message' => 'Mentorship request sent successfully.',
            'session' => $session,
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Sessions list (outgoing / incoming)
    // ──────────────────────────────────────────────────────────────────────────

    public function mySessions(Request $request)
    {
        $user = $request->user();

        $outgoing = MentorshipSession::where('mentee_id', $user->id)
            ->with(['mentor:id,name,expertise_area,status', 'review'])
            ->latest()
            ->get();

        $incoming = MentorshipSession::where('mentor_id', $user->id)
            ->with('mentee:id,name')
            ->latest()
            ->get();

        return response()->json(compact('outgoing', 'incoming'));
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

    // ──────────────────────────────────────────────────────────────────────────
    // Accept / decline / complete a session
    // ──────────────────────────────────────────────────────────────────────────

    public function updateStatus(Request $request, MentorshipSession $session)
    {
        if ($session->mentor_id !== $request->user()->id && !$request->user()->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status'       => 'required|in:accepted,declined,completed',
            'mentor_notes' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        // When mentor ACCEPTS: keep status as 'accepted' (pending conversation).
        // The conversation is NOT auto-created here; it starts when the scheduled
        // time arrives and someone opens the chat.
        if ($validated['status'] === 'accepted') {
            // Derive scheduled_at from the requested date + time if not provided explicitly
            $scheduledAt = $validated['scheduled_at']
                ?? ($session->requested_date && $session->requested_time_from
                    ? $session->requested_date . ' ' . $session->requested_time_from
                    : null);

            $session->update([
                'status'       => 'accepted',
                'mentor_notes' => $validated['mentor_notes'] ?? $session->mentor_notes,
                'scheduled_at' => $scheduledAt,
            ]);

            // Notify the mentee that their session was accepted
            $session->load('mentee');
            $session->mentee->notify(new \App\Notifications\SessionAcceptedNotification($session));

            return response()->json([
                'message' => 'Session accepted. Conversation will be available at the scheduled time.',
                'session' => $session->fresh(),
            ]);
        }

        // Declined or completed
        $session->update([
            'status'       => $validated['status'],
            'mentor_notes' => $validated['mentor_notes'] ?? $session->mentor_notes,
            'scheduled_at' => $validated['scheduled_at'] ?? $session->scheduled_at,
        ]);

        return response()->json([
            'message' => 'Session updated.',
            'session' => $session->fresh(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Open / start the conversation for an accepted session
    // ──────────────────────────────────────────────────────────────────────────

    public function startConversation(Request $request, MentorshipSession $session)
    {
        $user = $request->user();

        if ($session->mentee_id !== $user->id && $session->mentor_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($session->status !== 'accepted') {
            return response()->json(['message' => 'Session is not accepted yet.'], 422);
        }

        // Only allow starting on or after the scheduled time
        if ($session->scheduled_at && now()->lt($session->scheduled_at)) {
            return response()->json([
                'message' => 'The session has not started yet. It is scheduled for '
                           . $session->scheduled_at->format('F j, Y \a\t g:i A') . '.',
            ], 422);
        }

        // Find or create the conversation
        $existing = Conversation::where('is_group', false)
            ->whereHas('participants', fn($q) => $q->where('user_id', $session->mentor_id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $session->mentee_id))
            ->first();

        if (!$existing) {
            $existing = Conversation::create(['is_group' => false]);
            $existing->participants()->attach([$session->mentor_id, $session->mentee_id]);
        }

        // Mark conversation as started
        if (!$session->conversation_started_at) {
            $session->update(['conversation_started_at' => now()]);
        }

        return response()->json([
            'message'      => 'Conversation ready.',
            'conversation' => $existing->load('participants'),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Submit a review after a completed session
    // ──────────────────────────────────────────────────────────────────────────

    public function submitReview(Request $request, MentorshipSession $session)
    {
        $user = $request->user();

        if ($session->mentee_id !== $user->id) {
            return response()->json(['message' => 'Only the mentee can leave a review.'], 403);
        }

        if ($session->status !== 'completed') {
            return response()->json(['message' => 'You can only review completed sessions.'], 422);
        }

        if ($session->review()->exists()) {
            return response()->json(['message' => 'You have already reviewed this session.'], 422);
        }

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = MentorReview::create([
            'mentorship_session_id' => $session->id,
            'reviewer_id'           => $user->id,
            'mentor_id'             => $session->mentor_id,
            'rating'                => $validated['rating'],
            'comment'               => $validated['comment'] ?? null,
        ]);

        return response()->json([
            'message' => 'Review submitted. Thank you for your feedback!',
            'review'  => $review,
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Mentor reviews (public)
    // ──────────────────────────────────────────────────────────────────────────

    public function mentorReviews(User $mentor)
    {
        $reviews = MentorReview::where('mentor_id', $mentor->id)
            ->with('reviewer:id,name')
            ->latest()
            ->get();

        return response()->json([
            'average_rating' => round($reviews->avg('rating') ?? 0, 1),
            'total'          => $reviews->count(),
            'reviews'        => $reviews,
        ]);
    }
}
