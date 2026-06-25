<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MentorDeactivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly User $mentor,
        public readonly int  $totalMissed,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        // notify both the mentor and admins.
        // The body differs slightly based on who is receiving it.
        $isAdmin = $notifiable->role === 'admin';

        return [
            'type'         => 'mentor_deactivated',
            'title'        => $isAdmin ? 'Mentor Account Deactivated' : 'Your Account Has Been Deactivated',
            'body'         => $isAdmin
                ? "Mentor {$this->mentor->name} has been automatically deactivated after {$this->totalMissed} missed sessions."
                : "Your mentor account has been deactivated due to {$this->totalMissed} missed sessions. Please contact support to appeal.",
            'mentor_id'    => $this->mentor->id,
            'mentor_name'  => $this->mentor->name,
            'total_missed' => $this->totalMissed,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isAdmin = $notifiable->role === 'admin';

        $mail = (new MailMessage)->subject(
            $isAdmin
                ? "Mentor Deactivated: {$this->mentor->name}"
                : 'Your Mentor Account Has Been Deactivated'
        );

        if ($isAdmin) {
            return $mail
                ->greeting('Hello Admin,')
                ->line("Mentor **{$this->mentor->name}** (ID: {$this->mentor->id}) has been automatically deactivated.")
                ->line("Reason: {$this->totalMissed} missed sessions (threshold reached).")
                ->action('Review Mentor', url("/admin/mentors/{$this->mentor->id}"))
                ->line('You can reactivate this mentor from the admin panel if appropriate.');
        }

        return $mail
            ->greeting("Hello {$this->mentor->name},")
            ->line("Your mentor account has been **deactivated** because you missed {$this->totalMissed} scheduled sessions.")
            ->line('Missing sessions affects the mentees who depend on your guidance.')
            ->line('If you believe this is a mistake or would like to appeal, please contact our support team.')
            ->action('Contact Support', url('/support'))
            ->line('Thank you for your previous contributions to Tithandizane Women Hub.');
    }
}
