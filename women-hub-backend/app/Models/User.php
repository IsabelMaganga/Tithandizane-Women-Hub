<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'bio',
        'expertise_area', 'is_available', 'available_days',
        'available_time_start', 'available_time_end',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'available_days' => 'array',
        'is_available' => 'boolean',
    ];

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function mentorshipSessionsAsMentor()
    {
        return $this->hasMany(MentorshipSession::class, 'mentor_id');
    }

    public function mentorshipSessionsAsMentee()
    {
        return $this->hasMany(MentorshipSession::class, 'mentee_id');
    }
}