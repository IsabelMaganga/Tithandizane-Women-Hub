<?php

namespace App\Notifications;

use App\Models\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class SessionAcceptedNotification extends Notification
{
    use Queueable;

    public function __construct(private MentorshipSession $session) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $session      = $this->session;
        $scheduledAt  = $session->scheduled_at?->format('F j, Y \a\t g:i A') ?? 'To be confirmed';

        return (new MailMessage)
            ->subject('Your Mentorship Session Has Been Accepted')
            ->greeting("Hello {$notifiable->name},")
            ->line("Great news! {$session->mentor?->name} has accepted your mentorship session request.")
            ->line("Session details:")
            ->line("• Topic: {$session->topic}")
            ->line("• Date & Time: {$scheduledAt}")
            ->line("You will be able to start the conversation at the scheduled time.")
            ->action('Go to Sessions', url('/sessions'));
    }

    public function toArray(object $notifiable): array
    {
        $scheduledAt = $this->session->scheduled_at?->format('F j, Y \a\t g:i A') ?? 'To be confirmed';

        return [
            'title'      => 'Mentorship Session Accepted',
            'type'       => 'session_accepted',
            'session_id' => $this->session->id,
            'message'    => "Your session with {$this->session->mentor?->name} has been accepted for {$scheduledAt}.",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}