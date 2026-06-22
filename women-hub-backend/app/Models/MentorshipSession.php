<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MentorshipSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'topic',
        'message',
        'status',
        'mentor_notes',
        'scheduled_at',
        'requested_date',
        'requested_time_from',
        'requested_time_to',
        'is_missed',
        'missed_at',
        'conversation_started_at',
    ];

    protected $casts = [
        'scheduled_at'           => 'datetime',
        'missed_at'              => 'datetime',
        'conversation_started_at'=> 'datetime',
        'is_missed'              => 'boolean',
    ];

    // ──────────────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────────────

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function review()
    {
        return $this->hasOne(MentorReview::class, 'mentorship_session_id');
    }

    // ──────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────

    /**
     * The window after scheduled_at within which the conversation must start
     * before the session is flagged as missed (in minutes).
     */
    public const GRACE_PERIOD_MINUTES = 15;

    /**
     * Return true when the session window has passed and no conversation was started.
     */
    public function shouldBeFlaggedAsMissed(): bool
    {
        if ($this->status !== 'accepted' || $this->is_missed || $this->conversation_started_at) {
            return false;
        }

        if (!$this->scheduled_at) {
            return false;
        }

        $deadline = $this->scheduled_at->copy()->addMinutes(self::GRACE_PERIOD_MINUTES);

        return now()->greaterThan($deadline);
    }
}
