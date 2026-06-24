<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarassmentReport extends Model
{
    use HasFactory;

    protected $table = 'harassment_reports';

    protected $fillable = [
        'reference_number',
        'incident_type',
        'incident_title',
        'incident_description',
        'incident_date',
        'incident_location',
        'perpetrator_info',
        'is_anonymous',
        'victim_name',
        'victim_email',
        'victim_phone',
        'status',
        'severity',
        'admin_response',
        'responded_at',
        'assigned_mentor_id',
        'user_id'
    ];

    protected $casts = [
        'incident_date' => 'date',
        'is_anonymous' => 'boolean',
        'responded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($report) {
            if (!$report->reference_number) {
                do {
                    $report->reference_number = 'TWH-' . now()->year . '-' . strtoupper(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(6))), 0, 6));
                } while (static::where('reference_number', $report->reference_number)->exists());
            }
        });
    }

    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    /**
     * Get the mentor assigned to this report
     */
    public function assignedMentor()
    {
        return $this->belongsTo(User::class, 'assigned_mentor_id');
    }

    /**
     * Get the user who submitted this report (if not anonymous and logged in)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all notifications for this report
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'report_id');
    }

    public function resolveOwnerUserId(): ?int
    {
        if ($this->user_id) {
            return (int) $this->user_id;
        }

        if (!$this->is_anonymous && $this->victim_email) {
            $userId = User::where('email', $this->victim_email)->value('id');
            return $userId ? (int) $userId : null;
        }

        return null;
    }

    public function notifyOwner(string $type, string $title, string $message, array $data = []): void
    {
        $userId = $this->resolveOwnerUserId();
        if (!$userId) {
            return;
        }

        Notification::create([
            'type' => $type,
            'user_id' => $userId,
            'report_id' => $this->id,
            'title' => $title,
            'message' => $message,
            'data' => array_merge([
                'report_id' => $this->id,
                'reference_number' => $this->reference_number,
            ], $data),
            'is_read' => false,
        ]);
    }

    // ============================================
    // ACCESSORS
    // ============================================
    
    /**
     * Get the status color for badges
     */
    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'yellow',
            'reviewing' => 'blue',
            'assigned' => 'purple',
            'resolved' => 'green',
            'dismissed' => 'red',
        ][$this->status] ?? 'gray';
    }

    /**
     * Get the type color for badges
     */
    public function getTypeColorAttribute()
    {
        return [
            'physical' => 'purple',
            'verbal' => 'red',
            'sexual' => 'orange',
            'cyber' => 'teal',
            'other' => 'gray',
        ][$this->incident_type] ?? 'gray';
    }

    /**
     * Get the status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'reviewing' => 'bg-blue-100 text-blue-800',
            'assigned' => 'bg-purple-100 text-purple-800',
            'resolved' => 'bg-green-100 text-green-800',
            'dismissed' => 'bg-red-100 text-red-800',
        ];
        
        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        return "<span class='px-2 py-1 text-xs font-semibold rounded-full {$color}'>" . ucfirst($this->status) . "</span>";
    }

    /**
     * Get the incident type badge HTML
     */
    public function getTypeBadgeAttribute()
    {
        $colors = [
            'physical' => 'bg-purple-100 text-purple-800',
            'verbal' => 'bg-red-100 text-red-800',
            'sexual' => 'bg-orange-100 text-orange-800',
            'cyber' => 'bg-teal-100 text-teal-800',
            'other' => 'bg-gray-100 text-gray-800',
        ];
        
        $color = $colors[$this->incident_type] ?? 'bg-gray-100 text-gray-800';
        
        return "<span class='px-2 py-1 text-xs font-semibold rounded-full {$color}'>" . ucfirst($this->incident_type) . "</span>";
    }

    /**
     * Get the submitter name (either anonymous or actual name)
     */
    public function getSubmitterNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }
        
        if ($this->user) {
            return $this->user->name;
        }
        
        return $this->victim_name ?? 'Unknown';
    }

    /**
     * Get the submitter email (or null if anonymous)
     */
    public function getSubmitterEmailAttribute()
    {
        if ($this->is_anonymous) {
            return null;
        }
        
        if ($this->user) {
            return $this->user->email;
        }
        
        return $this->victim_email;
    }

    // ============================================
    // SCOPES
    // ============================================
    
    /**
     * Scope a query to only include pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include reports assigned to a specific mentor
     */
    public function scopeAssignedToMentor($query, $mentorId)
    {
        return $query->where('assigned_mentor_id', $mentorId);
    }

    /**
     * Scope a query to only include reports that need attention
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', ['pending', 'reviewing', 'assigned']);
    }

    /**
     * Scope a query to only include resolved reports
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope a query to only include anonymous reports
     */
    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    /**
     * Scope a query to only include non-anonymous reports
     */
    public function scopeNonAnonymous($query)
    {
        return $query->where('is_anonymous', false);
    }

    // ============================================
    // HELPER METHODS
    // ============================================
    
    /**
     * Check if the report is assigned to a mentor
     */
    public function isAssigned()
    {
        return !is_null($this->assigned_mentor_id);
    }

    /**
     * Check if the report can be assigned
     */
    public function canBeAssigned()
    {
        return in_array($this->status, ['pending', 'reviewing', 'assigned']);
    }

    /**
     * Check if the report is resolved
     */
    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if the report is dismissed
     */
    public function isDismissed()
    {
        return $this->status === 'dismissed';
    }

    /**
     * Mark the report as assigned to a mentor
     */
    public function markAsAssigned($mentorId)
    {
        $this->update([
            'assigned_mentor_id' => $mentorId,
            'status' => 'assigned'
        ]);
    }

    /**
     * Mark the report as resolved
     */
    public function markAsResolved($response = null)
    {
        $this->update([
            'status' => 'resolved',
            'admin_response' => $response,
            'responded_at' => now()
        ]);
    }

    /**
     * Mark the report as dismissed
     */
    public function markAsDismissed($reason = null)
    {
        $this->update([
            'status' => 'dismissed',
            'admin_response' => $reason,
            'responded_at' => now()
        ]);
    }

    /**
     * Get the time elapsed since the report was created
     */
    public function getTimeElapsedAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}