<?php

namespace App\Console\Commands;

use App\Models\MentorshipSession;
use App\Models\User;
use App\Notifications\SessionMissedNotification;
use Illuminate\Console\Command;

class CheckMissedSessions extends Command
{
    protected $signature   = 'mentorship:check-missed';
    protected $description = 'Flag accepted sessions with no conversation started past the grace period, '
                           . 'notify the mentee, and deactivate mentors who miss too many sessions.';

    /** How many missed sessions before a mentor is deactivated. */
    private const MISS_LIMIT = 5;

    public function handle(): int
    {
        $this->info('Checking for missed mentorship sessions…');

        // Find accepted sessions whose window has passed with no conversation started
        $candidates = MentorshipSession::where('status', 'accepted')
            ->where('is_missed', false)
            ->whereNull('conversation_started_at')
            ->whereNotNull('scheduled_at')
            ->get();

        $flagged = 0;

        foreach ($candidates as $session) {
            if (!$session->shouldBeFlaggedAsMissed()) {
                continue;
            }

            // Mark the session as missed
            $session->update([
                'is_missed' => true,
                'missed_at' => now(),
                'status'    => 'missed',
            ]);

            // Notify the mentee
            $session->mentee?->notify(new SessionMissedNotification($session, 'mentee'));

            // Increment the mentor's missed counter
            $mentor = $session->mentor;
            if ($mentor) {
                $mentor->increment('missed_sessions_count');

                // Deactivate mentor if threshold reached
                if ($mentor->missed_sessions_count >= self::MISS_LIMIT) {
                    $mentor->update(['status' => 'inactive', 'is_active' => false]);
                    $mentor->notify(new SessionMissedNotification($session, 'mentor_deactivated'));
                    $this->warn("Mentor #{$mentor->id} ({$mentor->name}) deactivated after "
                              . self::MISS_LIMIT . " missed sessions.");
                } else {
                    $mentor->notify(new SessionMissedNotification($session, 'mentor'));
                }
            }

            $flagged++;
            $this->line("Session #{$session->id} flagged as missed.");
        }

        $this->info("Done. {$flagged} session(s) flagged.");

        return self::SUCCESS;
    }
}
