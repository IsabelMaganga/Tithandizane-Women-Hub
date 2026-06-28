<?php
use Illuminate\Support\Facades\{Auth, Broadcast};
use App\Models\User;
use App\Models\Conversation;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    // ✅ FIXED: check conversation_participants table directly
    // instead of relying on a relationship that may not exist on this guard
    return \DB::table('conversation_participants')
        ->where('user_id', $user->id)
        ->where('conversation_id', $conversationId)
        ->exists();
});

Broadcast::channel('presence-global', function ($user) {
    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});

Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    return (int) $user->id === (int) $id;
});