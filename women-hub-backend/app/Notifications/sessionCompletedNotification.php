<?php

namespace App\Notifications;

use App\Models\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SessionCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(private MentorshipSession $session) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Mentorship Session Has Ended — Leave a Review')
            ->greeting("Hello {$notifiable->name},")
            ->line(
                "Your mentorship session with {$this->session->mentor?->name} has been completed."
            )
            ->line(
                "We'd love to hear how it went. Please take a moment to rate your experience."
            )
            ->action('Leave a Review', url("/mentorship/sessions/{$this->session->id}/review"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'      => 'Session Completed',
            'type'       => 'session_completed',
            'session_id' => $this->session->id,
            'message'    => "Your session with {$this->session->mentor?->name} is complete. Please leave a review.",
        ];
    }
}