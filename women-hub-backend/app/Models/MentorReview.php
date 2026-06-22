<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MentorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentorship_session_id',
        'reviewer_id',
        'mentor_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(MentorshipSession::class, 'mentorship_session_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
