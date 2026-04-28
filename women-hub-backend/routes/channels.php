<?php

use Illuminate\Support\Facades\{Auth, Broadcast};
use App\Models\User;

Broadcast::channel('chat.{conversationId}', function ($user, $conversationId) {
    return $user->conversations()
        ->where('conversation_id', $conversationId)
        ->exists();
});

Broadcast::channel('presence-global', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});

// request channel
Broadcast::channel('App.Models.User.{id}', function (User $user, $id) {
    // $user is already the authenticated user from Sanctum
    // Just check if the authenticated user ID matches the channel ID
    return (int) $user->id === (int)$id;
});
