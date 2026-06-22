<?php

namespace App\Services;

use App\Models\User;
use App\Models\MentorshipSession;
use Carbon\Carbon;

class MentorAvailabilityService
{
    /**
     * Validate that a mentor is available on the requested date/time slot.
     *
     * Returns null on success, or an error-message string on failure.
     */
    public function check(User $mentor, string $date, string $timeFrom, string $timeTo): ?string
    {
        $requestedDate = Carbon::parse($date);

        // ── 1. Day-of-week check ──────────────────────────────────────────────
        $dayName = strtolower($requestedDate->format('l')); // e.g. "monday"

        // available_days is double-encoded in the DB:
        // raw = "\"[\\\"Monday\\\",...]\"" → cast still gives a string "[\"Monday\",...]"
        // Decode repeatedly until we get an actual array.
        $decoded = $mentor->getRawOriginal('available_days');
        for ($i = 0; $i < 3; $i++) {
            if (is_array($decoded)) break;
            $decoded = json_decode($decoded, true);
        }

        $availableDays = collect(is_array($decoded) ? $decoded : [])
            ->map(fn($d) => strtolower(trim($d)));

        if (!$availableDays->contains($dayName)) {
            $days = $availableDays->map(fn($d) => ucfirst($d))->implode(', ');
            return "This mentor is not available on " . ucfirst($dayName) . "s. "
                 . "They are available on: " . $days . ".";
        }

        // ── 2. Time-window check ─────────────────────────────────────────────
        $mentorFrom = Carbon::parse($mentor->available_time_start);
        $mentorTo   = Carbon::parse($mentor->available_time_end);
        $reqFrom    = Carbon::parse($timeFrom);
        $reqTo      = Carbon::parse($timeTo);

        if ($reqFrom->lt($mentorFrom) || $reqTo->gt($mentorTo)) {
            return sprintf(
                "This mentor is only available between %s and %s. You requested %s – %s.",
                $mentorFrom->format('g:i A'),
                $mentorTo->format('g:i A'),
                $reqFrom->format('g:i A'),
                $reqTo->format('g:i A')
            );
        }

        // ── 3. Existing-session conflict check ───────────────────────────────
        $conflict = MentorshipSession::where('mentor_id', $mentor->id)
            ->where('requested_date', $date)
            ->whereIn('status', ['pending', 'accepted'])
            ->where(function ($q) use ($timeFrom, $timeTo) {
                $q->where('requested_time_from', '<', $timeTo)
                  ->where('requested_time_to',   '>',  $timeFrom);
            })
            ->exists();

        if ($conflict) {
            return "This mentor already has a session booked during that time on "
                 . $requestedDate->format('F j, Y') . ". Please choose a different time.";
        }

        return null; // all good
    }
}