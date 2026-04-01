<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChatRequestNotification extends Notification
{
    use Queueable;

    public $chat;
    public function __construct($chat)
    {
        $this->chat = $chat;
    }


    public function via( $notifiable )
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase( $notifiable )
    {
        return [
            'title' => "New Chat Request",
            'chat_id' => $this->chat->id,
            'message' => "You have a new chat request from " . $this->chat->mentee->name,
        ];
    }

    public function toBroadcast( $notifiable )
    {
        return [
            'title' => "New Chat Request",
            'chat_id' => $this->chat->id,
            'name' =>  $this->chat->mentee->name,
            'message' => "New chat request",
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
