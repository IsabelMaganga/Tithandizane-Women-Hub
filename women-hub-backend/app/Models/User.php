<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'location',
        'photo',
        'expertise_area',
        'bio',
        'status',
        'is_available',
        'available_days',
        'available_time_start',
        'available_time_end',
        'linkedin_url',
        'twitter_url',
        'website_url',
        'notes',
        'notify_welcome',
        'notify_training',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
    'available_days' => 'array',
    'expertise_area' => 'array', // Added this since expertise is also an array
    'is_available' => 'boolean', // Changed from is_available to match fillable
    'notify_welcome' => 'boolean',
    'notify_training' => 'boolean',
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

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Add 'photo_url' to the list of attributes appended to the JSON form
protected $appends = ['photo_url'];

public function getPhotoUrlAttribute()
{
    if (!$this->photo) {
        return null; 
    }
    
    // If the photo is a full URL (like from your seeder), return it directly
    if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
        return $this->photo;
    }

    // Otherwise, assume it's a local file in storage
    return asset('storage/' . $this->photo);
}
}