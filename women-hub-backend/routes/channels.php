<?php
use Illuminate\Support\Facades\Broadcast;

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