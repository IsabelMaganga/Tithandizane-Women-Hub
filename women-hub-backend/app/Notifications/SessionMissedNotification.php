<?php

namespace App\Notifications;

use App\Models\MentorshipSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionMissedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param MentorshipSession $session
     * @param string            $recipientRole  'mentor' | 'mentee'
     */
    public function __construct(
        public readonly MentorshipSession $session,
        public readonly string $recipientRole = 'mentee'
    ) {}

    public function via(object $notifiable): array
    {
        // database = stored in notifications table (pulled by NotificationController)
        // mail     = email delivery
        return ['database', 'mail'];
    }

    // ── Database payload (what the mobile app reads) ──────────────────────────

    public function toDatabase(object $notifiable): array
    {
        $isMentor    = $this->recipientRole === 'mentor';
        $otherPerson = $isMentor
            ? ($this->session->mentee?->name ?? 'your mentee')
            : ($this->session->mentor?->name ?? 'your mentor');

        return [
            'type'       => 'session_missed',
            'session_id' => $this->session->id,
            'topic'      => $this->session->topic,
            'title'      => $isMentor
                ? 'Session Missed'
                : 'Your Session Was Not Attended',
            'body'       => $isMentor
                ? "You missed your scheduled session on \"{$this->session->topic}\" with {$otherPerson}."
                : "Your mentor {$otherPerson} did not attend the session on \"{$this->session->topic}\".",
            'action_url' => "/sessions/{$this->session->id}",
        ];
    }

    // ── Email ─────────────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $isMentor    = $this->recipientRole === 'mentor';
        $otherPerson = $isMentor
            ? ($this->session->mentee?->name ?? 'your mentee')
            : ($this->session->mentor?->name ?? 'your mentor');

        $scheduledDate = optional($this->session->scheduled_at)->format('F j, Y \a\t g:i A')
            ?? ($this->session->requested_date . ' ' . $this->session->requested_time_from);

        return (new MailMessage)
            ->subject($isMentor ? 'You Missed a Session' : 'Your Mentor Missed Your Session')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line(
                $isMentor
                    ? "You missed your scheduled mentorship session on \"{$this->session->topic}\" with {$otherPerson}."
                    : "Unfortunately, your mentor {$otherPerson} did not attend your scheduled session on \"{$this->session->topic}\"."
            )
            ->line("Scheduled time: {$scheduledDate}")
            ->line(
                $isMentor
                    ? 'Please reach out to your mentee to reschedule.'
                    : 'You may request a new session at your convenience.'
            )
            ->action('View Session', url("/sessions/{$this->session->id}"))
            ->line('Thank you for using Tithandizane Women Hub.');
    }
}