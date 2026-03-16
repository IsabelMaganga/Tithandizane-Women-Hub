<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\MentorshipSession;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'message' => 'required|string',
            'is_anonymous' => 'boolean'
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false
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

        $messages->transform(function ($message) {
            if ($message->is_anonymous) {
                $message->setRelation('sender', [
                    'id' => null,
                    'name' => 'Anonymous User',
                    'avatar' => null 
                ]);
            }
            return $message;
        });

        return response()->json($messages);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'is_group' => 'required|boolean',
            'users' => 'nullable|array',
            'name' => 'nullable|string',
            'target_user_id' => 'nullable|exists:users,id', 
        ]);

        $user = auth()->user();

        // 1. Group Creation Logic (Admin OR Mentor)
        if ($request->is_group) {
            // Updated to allow both roles
            if (!$user->isAdmin() && $user->role !== 'mentor') {
                return response()->json(['message' => 'Only admins and mentors can create groups.'], 403);
            }
        } 
        // 2. 1-on-1 Chat Logic
        else {
            $targetUser = User::findOrFail($request->target_user_id);

            if ($targetUser->role === 'mentor' && !$user->isAdmin() && $user->role !== 'mentor') {
                $hasApprovedSession = MentorshipSession::where('mentee_id', $user->id)
                    ->where('mentor_id', $targetUser->id)
                    ->where('status', 'accepted')
                    ->exists();

                if (!$hasApprovedSession) {
                    return response()->json(['message' => 'Approved mentorship session required.'], 403);
                }
            }
            
            $existingConvo = Conversation::where('is_group', false)
                ->whereHas('participants', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->whereHas('participants', function($q) use ($targetUser) {
                    $q->where('user_id', $targetUser->id);
                })->first();

            if ($existingConvo) {
                return response()->json($existingConvo);
            }
        }

        $conversation = Conversation::create([
            'name' => $request->is_group ? $request->name : null,
            'is_group' => $request->is_group,
        ]);

        if ($request->is_group) {
            $participants = $request->users ?? [];
            $participants[] = $user->id; 
            $conversation->participants()->attach(array_unique($participants));
        } else {
            $conversation->participants()->attach([
                $user->id,
                $request->target_user_id
            ]);
        }

        return response()->json($conversation);
    }

    //Allow users to join a group
    public function joinGroup($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);

        if (!$conversation->is_group) {
            return response()->json(['message' => 'This is not a group chat.'], 422);
        }

        // Add the user to the group
        $conversation->participants()->syncWithoutDetaching([auth()->id()]);

        return response()->json(['message' => 'Successfully joined the group']);
    }

    //List groups available to join
    public function getAvailableGroups()
    {
        $groups = Conversation::where('is_group', true)
            ->whereDoesntHave('participants', function($q) {
                $q->where('user_id', auth()->id());
            })->get();

        return response()->json($groups);
    }

    public function getConversations()
    {
        $user = auth()->user();

        $conversations = $user->conversations()
            ->with(['participants', 'messages' => function ($q) {
                $q->latest()->limit(1); 
            }])
            ->get();

        return response()->json($conversations);
    }
}