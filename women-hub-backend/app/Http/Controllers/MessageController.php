<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\HarassmentReport;
use App\Models\MentorshipSession;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message'         => 'required|string',
            'is_anonymous'    => 'boolean',
        ]);

        // Prevent sending to a conversation whose session has been completed/terminated
        $conversation = Conversation::findOrFail($request->conversation_id);

        $terminatedSession = MentorshipSession::where('status', 'completed')
            ->whereHas('conversation', fn($q) => $q->where('id', $conversation->id))
            ->exists();

        if ($terminatedSession) {
            return response()->json([
                'message' => 'This session has been completed. No further messages can be sent.',
            ], 403);
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id'       => auth()->id(),
            'message'         => $request->message,
            'is_anonymous'    => $request->is_anonymous ?? false,
        ]);

        $message->load('sender');

        // Trigger WebSocket broadcast
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessages($conversationId)
    {
        $messages = Message::where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark all messages sent by others in this conversation as read
        Message::where('conversation_id', $conversationId)
            ->where('sender_id', '!=', auth()->id())
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messages->transform(function ($message) {
            if ($message->is_anonymous) {
                $message->setRelation('sender', [
                    'id'     => null,
                    'name'   => 'Anonymous User',
                    'avatar' => null,
                ]);
            }
            return $message;
        });

        return response()->json($messages);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'is_group'              => 'nullable|boolean',
            'users'                 => 'nullable|array',
            'name'                  => 'nullable|string',
            'target_user_id'        => 'nullable|exists:users,id',
            'mentor_id'             => 'nullable|exists:users,id',
            'receiver_id'           => 'nullable|exists:users,id',
            'harassment_report_id'  => 'nullable|exists:harassment_reports,id',
        ]);

        $user        = auth()->user();
        $isGroup     = $request->boolean('is_group', false);
        $targetUserId = $request->target_user_id
            ?? $request->mentor_id
            ?? $request->receiver_id;

        if ($isGroup) {
            if (!$user->isAdmin() && $user->role !== 'mentor') {
                return response()->json([
                    'message' => 'Only admins and mentors can create groups.',
                ], 403);
            }
        } else {
            if (!$targetUserId) {
                return response()->json([
                    'message' => 'Target user is required.',
                ], 422);
            }

            $targetUser = User::findOrFail($targetUserId);

            if (
                $targetUser->role === 'mentor' &&
                !$user->isAdmin() &&
                $user->role !== 'mentor'
            ) {
                $hasApprovedSession = MentorshipSession::where('mentee_id', $user->id)
                    ->where('mentor_id', $targetUser->id)
                    ->where('status', 'accepted')
                    ->exists();

                $hasReportAssignment = HarassmentReport::where('user_id', $user->id)
                    ->where('assigned_mentor_id', $targetUser->id)
                    ->where('is_anonymous', false)
                    ->exists();

                if (!$hasApprovedSession && !$hasReportAssignment) {
                    return response()->json([
                        'message' => 'Approved mentorship session or identified harassment report required.',
                    ], 403);
                }
            }

            // Return existing conversation if it already exists
            $existingConvo = Conversation::where('is_group', false)
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn($q) => $q->where('user_id', $targetUser->id))
                ->first();

            if ($existingConvo) {
                return response()->json($existingConvo->load('participants'));
            }
        }

        $conversation = Conversation::create([
            'name'     => $isGroup ? $request->name : null,
            'is_group' => $isGroup,
        ]);

        if ($isGroup) {
            $participants   = $request->users ?? [];
            $participants[] = $user->id;
            $conversation->participants()->attach(array_unique($participants));
        } else {
            $conversation->participants()->attach([$user->id, $targetUserId]);
        }

        return response()->json($conversation->load('participants'));
    }

    // Allow any authenticated user to join a group
    public function joinGroup($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->is_group) {
            return response()->json(['message' => 'This is not a group chat.'], 422);
        }

        $conversation->participants()->syncWithoutDetaching([auth()->id()]);

        return response()->json(['message' => 'Successfully joined the group.']);
    }

    // List groups the current user has not yet joined
    public function getAvailableGroups()
    {
        $groups = Conversation::where('is_group', true)
            ->whereDoesntHave('participants', fn($q) => $q->where('user_id', auth()->id()))
            ->get();

        return response()->json($groups);
    }

    public function getConversations()
    {
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with(['participants', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function ($conversation) use ($user) {
                $conversation->unread_count = $conversation->messages()
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_read', 0)
                    ->count();
                return $conversation;
            });

        return response()->json($conversations);
    }

    public function showInfo($id)
    {
        $conversation = Conversation::with('participants')->findOrFail($id);

        return response()->json([
            'id'           => $conversation->id,
            'name'         => $conversation->name,
            'is_group'     => $conversation->is_group,
            'participants' => $conversation->participants->map(fn($user) => [
                'id'       => $user->id,
                'name'     => $user->name,
                'is_admin' => $user->pivot->is_admin ?? false,
            ]),
        ]);
    }
}