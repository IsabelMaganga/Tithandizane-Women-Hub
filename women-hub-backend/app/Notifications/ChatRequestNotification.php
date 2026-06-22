<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ChatRequestNotification extends Notification
{
    use Queueable;

    public function __construct(public $chat) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'New Mentorship Request',
            'chat_id' => $this->chat->id,
            'message' => 'You have a new mentorship request from ' . $this->chat->mentee->name,
            'type'    => 'chat_request',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'   => 'New Mentorship Request',
            'chat_id' => $this->chat->id,
            'name'    => $this->chat->mentee->name,
            'message' => 'You have a new mentorship request from ' . $this->chat->mentee->name,
            'type'    => 'chat_request',
        ]);
    }
}