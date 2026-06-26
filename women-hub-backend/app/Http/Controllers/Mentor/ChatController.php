<?php

namespace App\Http\Controllers\Mentor;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\HarassmentReport;
use App\Models\Message;
use App\Models\MentorshipSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private function mentor()
    {
        return Auth::guard('mentor')->user();
    }

    public function index()
    {
        $mentor = $this->mentor();

        $conversations = Conversation::where('is_group', false)
            ->whereHas('participants', function ($query) use ($mentor) {
                $query->where('conversation_participants.user_id', $mentor->id);
            })
            ->with([
                'participants' => function ($query) {
                    $query->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.role'
                    );
                },
                'messages' => function ($query) {
                    $query->with([
                        'sender' => function ($q) {
                            $q->select(
                                'users.id',
                                'users.name',
                                'users.role'
                            );
                        }
                    ])
                    ->latest()
                    ->limit(1);
                },
            ])
            ->latest('updated_at')
            ->paginate(20);

        return view('mentor.chat.index', compact('conversations', 'mentor'));
    }

    public function openSessionConversation(MentorshipSession $session)
    {
        $mentor = $this->mentor();

        if (!$mentor) {
            abort(403);
        }

        if ($session->mentor_id !== $mentor->id) {
            abort(403);
        }

        $conversation = Conversation::where('session_id', $session->id)->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'name' => $session->topic ?? 'Mentorship Session',
                'is_group' => false,
                'session_id' => $session->id,
            ]);

            $conversation->participants()->attach([$session->mentor_id, $session->mentee_id]);
        }

        if ($session->conversation_id !== $conversation->id) {
            $session->forceFill(['conversation_id' => $conversation->id])->save();
        }

        if (!$session->conversation_started_at) {
            $session->update(['conversation_started_at' => now()]);
        }

        return redirect()->route('mentor.chat.show', $conversation);
    }

    public function show(Conversation $conversation)
    {
        $mentor = $this->mentor();

        $this->ensureMentorCanAccessConversation($conversation, $mentor);

        $session = $this->resolveMentorshipSession($conversation);
        $isCompletedSession = $session?->status === 'completed';

        $conversation->load([
            'participants' => function ($query) {
                $query->select(
                    'users.id',
                    'users.name',
                    'users.email',
                    'users.role'
                );
            },

            'messages' => function ($query) {
                $query->with([
                    'sender' => function ($q) {
                        $q->select(
                            'users.id',
                            'users.name',
                            'users.role'
                        );
                    }
                ])->orderBy('created_at');
            },
            'mentorshipSession' => function ($query) {
                $query->with('mentee');
            },
        ]);

        $other = $conversation->participants
            ->first(fn($participant) => $participant->id != $mentor->id);

        return view('mentor.chat.show', compact(
            'conversation',
            'mentor',
            'other',
            'session',
            'isCompletedSession'
        ));
    }

    public function openHarassmentReportChat(HarassmentReport $report)
    {
        $mentor = $this->mentor();

        $report = HarassmentReport::where('id', $report->id)
            ->where('assigned_mentor_id', $mentor->id)
            ->where('is_anonymous', false)
            ->whereNotNull('user_id')
            ->firstOrFail();

        $conversation = $this->conversationForReport($report, $mentor);

        return redirect()->route('mentor.chat.show', $conversation);
    }

    public function endSession(Request $request, Conversation $conversation)
    {
        $mentor = $this->mentor();

        $this->ensureMentorCanAccessConversation($conversation, $mentor);

        $session = $this->resolveMentorshipSession($conversation);

        if (!$session) {
            return back()->with('error', 'This chat is not linked to a mentorship session.');
        }

        if ($session->mentor_id !== $mentor->id) {
            abort(403);
        }

        if (!in_array($session->status, ['accepted'])) {
            return back()->with('error', 'Only an active accepted session can be ended.');
        }

        $session->forceFill([
            'status' => 'completed',
            'ended_at' => now(),
            'mentor_notes' => $request->input('mentor_notes', $session->mentor_notes),
        ])->save();

        return redirect()->route('mentor.chat')->with('success', 'Session ended successfully.');
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $mentor = $this->mentor();

        $this->ensureMentorCanAccessConversation($conversation, $mentor);

        $session = $this->resolveMentorshipSession($conversation);

        if ($session && $session->status === 'completed') {
            return back()->with('error', 'This session has already ended.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $mentor->id,
            'message'         => $request->message,
            'is_read'         => false,
        ]);

        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

        return back()->with('success', 'Message sent.');
    }

    private function resolveMentorshipSession(Conversation $conversation): ?MentorshipSession
    {
        return MentorshipSession::where(function ($query) use ($conversation) {
            $query->where('conversation_id', $conversation->id)
                ->orWhere('id', $conversation->session_id);
        })->first();
    }

    private function conversationForReport(HarassmentReport $report, $mentor): Conversation
    {
        $existing = Conversation::where('is_group', false)
            ->whereHas('participants', function ($query) use ($mentor) {
                $query->where('conversation_participants.user_id', $mentor->id);
            })
            ->whereHas('participants', function ($query) use ($report) {
                $query->where('conversation_participants.user_id', $report->user_id);
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        $conversation = Conversation::create([
            'name' => null,
            'is_group' => false,
        ]);

        $conversation->participants()->attach([
            $mentor->id,
            $report->user_id,
        ]);

        return $conversation;
    }

    private function ensureMentorCanAccessConversation(
        Conversation $conversation,
        $mentor
    ): void {
        if (!$mentor) {
            abort(403);
        }

        $allowed = $conversation->participants()
            ->where('conversation_participants.user_id', $mentor->id)
            ->exists();

        if (!$allowed) {
            abort(403);
        }
    }
}