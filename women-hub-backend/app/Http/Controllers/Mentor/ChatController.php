<?php

namespace App\Http\Controllers\Mentor;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\HarassmentReport;
use App\Models\Message;
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

    public function show(Conversation $conversation)
    {
        $mentor = $this->mentor();

        $this->ensureMentorCanAccessConversation($conversation, $mentor);

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
        ]);

        $other = $conversation->participants
            ->first(fn($participant) => $participant->id != $mentor->id);

        return view('mentor.chat.show', compact(
            'conversation',
            'mentor',
            'other'
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

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $mentor = $this->mentor();

        $this->ensureMentorCanAccessConversation($conversation, $mentor);

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