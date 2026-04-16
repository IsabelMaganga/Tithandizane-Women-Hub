<?php
// app/Models/Mentor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mentor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'mentors';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'location',
        'photo',
        'expertise',
        'bio',
        'status',
        'availability',
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'expertise' => 'array',
        'available_days' => 'array',
        'notify_welcome' => 'boolean',
        'notify_training' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
    
    // Accessor for photo URL
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return null;
    }
    
    // Helper method to get expertise as array
    public function getExpertiseArrayAttribute()
    {
        if (is_string($this->expertise)) {
            return json_decode($this->expertise, true) ?? [];
        }
        return $this->expertise ?? [];
    }
    
    // Helper method to get expertise as string for display
    public function getExpertiseStringAttribute()
    {
        $expertiseArray = $this->expertise_array;
        return implode(', ', $expertiseArray);
    }
    
    // Scopes for filtering
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}