<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorshipSession extends Model
{
    protected $fillable = [
        'mentor_id', 'mentee_id', 'topic', 'message',
        'status', 'scheduled_at', 'mentor_notes',
    ];

    protected $casts = ['scheduled_at' => 'datetime'];

    public function mentor() { return $this->belongsTo(User::class, 'mentor_id'); }
    public function mentee() { return $this->belongsTo(User::class, 'mentee_id'); }
}