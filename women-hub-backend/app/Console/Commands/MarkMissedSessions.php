<?php

namespace App\Console\Commands;

use App\Models\MentorshipSession;
use App\Models\User;
use App\Notifications\SessionMissedNotification;
use App\Notifications\MentorMissedWarningNotification;
use App\Notifications\MentorDeactivatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MarkMissedSessions extends Command
{
    protected $signature   = 'sessions:mark-missed';
    protected $description = 'Mark accepted sessions as missed when the scheduled time has passed without a conversation starting';

    // ── Thresholds ────────────────────────────────────────────────────────────
    private const WARN_AT       = 3;   // send warning notification at this count
    private const DEACTIVATE_AT = 5;   // deactivate mentor account at this count

    public function handle(): int
    {
        $now = now();

        // ── 1. Find all sessions that qualify as missed ───────────────────────
        $missed = MentorshipSession::where('status', 'accepted')
            ->whereNull('conversation_started_at')
            ->whereNotNull('requested_date')
            ->whereNotNull('requested_time_to')
            ->get()
            ->filter(function (MentorshipSession $session) use ($now) {
                $endAt = \Carbon\Carbon::parse(
                    $session->requested_date . ' ' . $session->requested_time_to
                );
                // 5-minute grace period
                return $now->greaterThan($endAt->addMinutes(5));
            });

        if ($missed->isEmpty()) {
            $this->info('No missed sessions found.');
            return self::SUCCESS;
        }

        // ── 2. Mark each session and collect affected mentor IDs ──────────────
        $affectedMentorIds = collect();

        foreach ($missed as $session) {
            try {
                $session->update([
                    'status'    => 'missed',
                    'missed_at' => now(),
                ]);

                $session->load('mentor', 'mentee');

                // Notify the mentee their session was missed
                $session->mentee?->notify(new SessionMissedNotification($session, 'mentee'));

                // Notify the mentor they missed a session
                $session->mentor?->notify(new SessionMissedNotification($session, 'mentor'));

                $affectedMentorIds->push($session->mentor_id);

                $this->info("Marked session #{$session->id} as missed.");
                Log::info('Session marked missed', ['session_id' => $session->id]);

            } catch (\Throwable $e) {
                Log::error('Failed to mark session missed', [
                    'session_id' => $session->id,
                    'error'      => $e->getMessage(),
                ]);
                $this->error("Failed on session #{$session->id}: {$e->getMessage()}");
            }
        }

        // ── 3. Check each affected mentor's total missed count ────────────────
        foreach ($affectedMentorIds->unique() as $mentorId) {
            $this->handleMentorMissedCount($mentorId);
        }

        $this->info("Done. Marked {$missed->count()} session(s) as missed.");
        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Check the mentor's total missed sessions and take action
    // ─────────────────────────────────────────────────────────────────────────

    private function handleMentorMissedCount(int $mentorId): void
    {
        $mentor = User::find($mentorId);
        if (!$mentor) return;

        // Count ALL time missed sessions for this mentor
        $totalMissed = MentorshipSession::where('mentor_id', $mentorId)
            ->where('status', 'missed')
            ->count();

        $this->info("Mentor #{$mentorId} ({$mentor->name}) has {$totalMissed} missed session(s).");

        // ── Deactivate at 5 ──────────────────────────────────────────────────
        if ($totalMissed >= self::DEACTIVATE_AT && $mentor->status === 'active') {
            $mentor->update(['status' => 'inactive']);

            // Notify the mentor their account has been deactivated
            $mentor->notify(new MentorDeactivatedNotification($mentor, $totalMissed));

            // Notify admins so they are aware
            User::where('role', 'admin')->each(function (User $admin) use ($mentor, $totalMissed) {
                $admin->notify(new MentorDeactivatedNotification($mentor, $totalMissed));
            });

            $this->warn("Mentor #{$mentorId} ({$mentor->name}) DEACTIVATED after {$totalMissed} missed sessions.");
            Log::warning('Mentor deactivated due to missed sessions', [
                'mentor_id'     => $mentorId,
                'total_missed'  => $totalMissed,
            ]);
            return;
        }

        // ── Warn at 3 (but not yet at 5) ─────────────────────────────────────
        if ($totalMissed >= self::WARN_AT) {
            $remaining = self::DEACTIVATE_AT - $totalMissed;

            $mentor->notify(new MentorMissedWarningNotification($mentor, $totalMissed, $remaining));

            $this->warn("Warning sent to mentor #{$mentorId} ({$mentor->name}): {$totalMissed} missed, {$remaining} left before deactivation.");
            Log::warning('Mentor missed session warning sent', [
                'mentor_id'    => $mentorId,
                'total_missed' => $totalMissed,
                'remaining'    => $remaining,
            ]);
        }
    }
}