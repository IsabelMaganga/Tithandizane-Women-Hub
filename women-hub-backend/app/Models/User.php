<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'phone', 
        'bio',
        'expertise_area', 
        'is_available', 
        'available_days',
        'available_time_start', 
        'available_time_end',
        'photo',
        'is_active',
        'specialization',
        'last_password_updated_at'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $dates = ['last_password_updated_at'];

    protected $casts = [
        'available_days' => 'array',
        'expertise_area' => 'array',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'notify_welcome' => 'boolean',
        'notify_training' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_password_updated_at' => 'datetime',
    ];

    // Add 'photo_url' to the list of attributes appended to the JSON form
    protected $appends = ['photo_url'];

    // ============================================
    // RELATIONSHIPS FOR HARASSMENT REPORT SYSTEM
    // ============================================
    
    /**
     * Get all reports assigned to this user (if user is a mentor)
     */
    public function assignedReports()
    {
        return $this->hasMany(HarassmentReport::class, 'assigned_mentor_id');
    }

    /**
     * Get all reports submitted by this user (if user is logged in and not anonymous)
     */
    public function submittedReports()
    {
        return $this->hasMany(HarassmentReport::class, 'user_id');
    }

    /**
     * Get all notifications for this user
     */
    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class);
    // }

    /**
     * Get unread notifications count
     */
    public function unreadNotifications()
    {
        return $this->notifications()->where('is_read', false);
    }

    // ============================================
    // EXISTING RELATIONSHIPS
    // ============================================

    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }

    public function mentorshipSessionsAsMentor()
    {
        return $this->hasMany(MentorshipSession::class, 'mentor_id');
    }

    public function mentorshipSessionsAsMentee()
    {
        return $this->hasMany(MentorshipSession::class, 'mentee_id');
    }

    public function guidanceContents()
    {
        return $this->hasMany(GuidanceContent::class, 'mentor_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function expertises()
{
    return $this->belongsToMany(Expertise::class, 'expertise_user');
}

    // ============================================
    // ACCESSORS & MUTATORS
    // ============================================

    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            // Return default avatar with user's name
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7c3aed&color=fff';
        }

        // If the photo is a full URL (like from your seeder), return it directly
        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        // Otherwise, assume it's a local file in storage
        return asset('storage/' . $this->photo);
    }

    /**
     * Get the user's role badge color
     */
    public function getRoleBadgeColorAttribute()
    {
        return match($this->role) {
            'admin' => 'purple',
            'mentor' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get the user's full name or display name
     */
    public function getDisplayNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get active cases count for mentors
     */
    public function getActiveCasesCountAttribute()
    {
        if ($this->isMentor()) {
            return $this->assignedReports()
                ->whereIn('status', ['assigned', 'reviewing'])
                ->count();
        }
        return 0;
    }

    /**
     * Get resolved cases count for mentors
     */
    public function getResolvedCasesCountAttribute()
    {
        if ($this->isMentor()) {
            return $this->assignedReports()
                ->where('status', 'resolved')
                ->count();
        }
        return 0;
    }

    /**
     * Scope for active users only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for mentors only
     */
    public function scopeMentors($query)
    {
        return $query->where('role', 'mentor');
    }

    /**
     * Scope for admins only
     */
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope for available mentors
     */
    public function scopeAvailableMentors($query)
    {
        return $query->where('role', 'mentor')
            ->where('is_active', true)
            ->where('is_available', true);
    }
}