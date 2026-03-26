<?php

namespace App\Events;

use App\Models\MentorshipSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $mentorId;
    public function __construct(MentorshipSession $chat, $mentorId)
    {
        $this->chat = $chat;
        $this->mentorId = $mentorId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('mentor.' . $this->mentorId);
    }

    public function broadcastAs()
    {
        return 'new-chat-request';
    }

    public function broadcastWith()
    {
        return [
            'id' =>$this->chat->id,
            'girl_name' =>$this->chat->mentee()->name,
            'created_at' =>$this->chat->created_at->diffForHumans()
        ];
    }
}
