<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mentor extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'location',
        'password',
        'photo',
        'expertise',
        'bio',
        'status',
        'availability',
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
        'notify_welcome' => 'boolean',
        'notify_training' => 'boolean',
        'email_verified_at' => 'datetime',
    ];
}