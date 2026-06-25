<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorMissedWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $mentor,
        public readonly int  $totalMissed,
        public readonly int  $remaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Stored in app_notifications.
     * Key is 'message' (not 'body') to match what the frontend reads.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'         => 'mentor_missed_warning',
            'title'        => 'Missed Sessions Warning',
            'message'      => "You have missed {$this->totalMissed} session(s). "
                            . "You will be deactivated after {$this->remaining} more missed session(s). "
                            . "Please attend your scheduled sessions.",
            'total_missed' => $this->totalMissed,
            'remaining'    => $this->remaining,
            'deactivate_at'=> $this->totalMissed + $this->remaining,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Warning: Missed Sessions')
            ->greeting("Hello {$this->mentor->name},")
            ->line("You have missed **{$this->totalMissed}** scheduled mentorship session(s).")
            ->line("Your account will be **deactivated** if you miss {$this->remaining} more session(s).")
            ->line('Please make sure to attend your upcoming sessions or reschedule them in advance.')
            ->action('View My Sessions', url('/sessions'))
            ->line('Thank you for being part of Tithandizane Women Hub.');
    }
}