<?php

namespace App\Notifications;

use App\Models\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SessionMissedNotification extends Notification
{
    use Queueable;

    /**
     * @param MentorshipSession $session
     * @param string $audience  'mentee' | 'mentor' | 'mentor_deactivated'
     */
    public function __construct(
        private MentorshipSession $session,
        private string $audience = 'mentee'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $session = $this->session;

        if ($this->audience === 'mentee') {
            return (new MailMessage)
                ->subject('Your Mentorship Session Was Missed')
                ->greeting("Hello {$notifiable->name},")
                ->line("Unfortunately your mentorship session scheduled for "
                     . $session->scheduled_at?->format('F j, Y \a\t g:i A')
                     . " with {$session->mentor?->name} was not started in time and has been marked as missed.")
                ->line("You can request a new session at any time.")
                ->action('Browse Mentors', url('/mentors'));
        }

        if ($this->audience === 'mentor_deactivated') {
            return (new MailMessage)
                ->subject('Your Account Has Been Deactivated')
                ->greeting("Hello {$notifiable->name},")
                ->line("Your account has been deactivated because you have missed "
                     . \App\Console\Commands\CheckMissedSessions::class::MISS_LIMIT
                     . " or more mentorship sessions.")
                ->line("Please contact an administrator to have your account reinstated.");
        }

        // mentor — regular missed warning
        return (new MailMessage)
            ->subject('Missed Mentorship Session')
            ->greeting("Hello {$notifiable->name},")
            ->line("A session scheduled for "
                 . $session->scheduled_at?->format('F j, Y \a\t g:i A')
                 . " with {$session->mentee?->name} was not started and has been marked as missed.")
            ->line("You have missed {$notifiable->missed_sessions_count} session(s) in total. "
                 . "Please note that reaching 5 missed sessions will deactivate your account.");
    }

    public function toArray(object $notifiable): array
{
    $title = match ($this->audience) {
        'mentee'             => 'Session Missed',
        'mentor_deactivated' => 'Account Deactivated',
        default              => 'Session Missed',
    };

    return [
        'title'      => $title,
        'type'       => 'session_missed',
        'audience'   => $this->audience,
        'session_id' => $this->session->id,
        'message'    => match ($this->audience) {
            'mentee'             => "Your session with {$this->session->mentor?->name} was missed.",
            'mentor_deactivated' => "Your account has been deactivated due to missed sessions.",
            default              => "Session with {$this->session->mentee?->name} was missed.",
        },
    ];
}
}
