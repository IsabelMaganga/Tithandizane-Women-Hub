<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['name', 'is_group'];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * The mentorship session this conversation was created for (if any).
     * Inverse of MentorshipSession::conversation().
     */
    public function mentorshipSession()
    {
        return $this->hasOne(MentorshipSession::class);
    }
}