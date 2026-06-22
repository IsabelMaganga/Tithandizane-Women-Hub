<?php

namespace App\Notifications;

use App\Models\MentorshipSession;
use App\Console\Commands\CheckMissedSessions;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SessionMissedNotification extends Notification
{
    use Queueable;

    /**
     * @param MentorshipSession $session
     * @param string            $audience  'mentee' | 'mentor' | 'mentor_deactivated'
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
        $session      = $this->session;
        $scheduledStr = $session->scheduled_at?->format('F j, Y \a\t g:i A') ?? 'an unknown time';

        return match ($this->audience) {

            'mentee' => (new MailMessage)
                ->subject('Your Mentorship Session Was Missed')
                ->greeting("Hello {$notifiable->name},")
                ->line(
                    "Unfortunately your mentorship session scheduled for {$scheduledStr} "
                    . "with {$session->mentor?->name} was not started in time and has been marked as missed."
                )
                ->line("You can request a new session at any time.")
                ->action('Browse Mentors', url('/mentors')),

            'mentor_deactivated' => (new MailMessage)
                ->subject('Your Mentor Account Has Been Deactivated')
                ->greeting("Hello {$notifiable->name},")
                ->line(
                    "Your mentor account has been deactivated because you have missed "
                    . CheckMissedSessions::MISS_LIMIT . " or more scheduled sessions."
                )
                ->line("Please contact an administrator to have your account reinstated.")
                ->action('Contact Support', url('/support')),

            // 'mentor' — regular missed-session warning
            default => (new MailMessage)
                ->subject('Missed Mentorship Session')
                ->greeting("Hello {$notifiable->name},")
                ->line(
                    "A session scheduled for {$scheduledStr} with "
                    . "{$session->mentee?->name} was not started and has been marked as missed."
                )
                ->line(
                    "You have now missed {$notifiable->missed_sessions_count} session(s) in total. "
                    . "Reaching " . CheckMissedSessions::MISS_LIMIT
                    . " missed sessions will deactivate your account."
                )
                ->action('View Your Sessions', url('/mentor/sessions')),
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'      => match ($this->audience) {
                'mentor_deactivated' => 'Account Deactivated',
                default              => 'Session Missed',
            },
            'type'       => 'session_missed',
            'audience'   => $this->audience,
            'session_id' => $this->session->id,
            'message'    => match ($this->audience) {
                'mentee'             => "Your session with {$this->session->mentor?->name} was missed.",
                'mentor_deactivated' => "Your account has been deactivated due to too many missed sessions.",
                default              => "Session with {$this->session->mentee?->name} was missed.",
            },
        ];
    }
}