<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'admin_response',
        'responded_at',
        'responded_by',
        'user_id',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'incident_date' => 'date',
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants for easier management
    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWING = 'reviewing';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_DISMISSED = 'dismissed';

    // Incident type constants
    const TYPE_PHYSICAL = 'physical';
    const TYPE_VERBAL = 'verbal';
    const TYPE_SEXUAL = 'sexual';
    const TYPE_CYBER = 'cyber';
    const TYPE_OTHER = 'other';

    // Generate reference number before creating
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            // More robust reference number generation
            $latest = static::latest('id')->first();
            $nextId = $latest ? $latest->id + 1 : 1;
            $report->reference_number = 'HR-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
        });
    }

    // Accessor for formatted reference number
    public function getFormattedReferenceAttribute()
    {
        return $this->reference_number ?? 'HR-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Accessor for status badge color
    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_REVIEWING => 'blue',
            self::STATUS_RESOLVED => 'green',
            self::STATUS_DISMISSED => 'red',
            default => 'gray',
        };
    }

    // Accessor for incident type badge color
    public function getTypeBadgeColorAttribute()
    {
        return match($this->incident_type) {
            self::TYPE_PHYSICAL => 'purple',
            self::TYPE_VERBAL => 'red',
            self::TYPE_SEXUAL => 'orange',
            self::TYPE_CYBER => 'teal',
            self::TYPE_OTHER => 'gray',
            default => 'gray',
        };
    }

    // Check if report is anonymous
    public function getIsAnonymousAttribute($value)
    {
        return (bool) $value;
    }

    // Get reporter display name
    public function getReporterNameAttribute()
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }
        
        return $this->victim_name ?? 'Unknown';
    }

    // Get reporter contact info
    public function getReporterContactAttribute()
    {
        if ($this->is_anonymous) {
            return null;
        }
        
        return [
            'name' => $this->victim_name,
            'email' => $this->victim_email,
            'phone' => $this->victim_phone,
        ];
    }

    // Check if report has been responded to
    public function getHasResponseAttribute()
    {
        return !is_null($this->admin_response);
    }

    // Check if report is resolved
    public function getIsResolvedAttribute()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function respondedBy()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeReviewing($query)
    {
        return $query->where('status', self::STATUS_REVIEWING);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', self::STATUS_DISMISSED);
    }

    public function scopeAnonymous($query)
    {
        return $query->where('is_anonymous', true);
    }

    public function scopeNonAnonymous($query)
    {
        return $query->where('is_anonymous', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('incident_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('incident_date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('incident_date', '<=', $to);
        }
        return $query;
    }

    public function scopeSubmittedBetween($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('incident_title', 'LIKE', "%{$search}%")
              ->orWhere('incident_description', 'LIKE', "%{$search}%")
              ->orWhere('reference_number', 'LIKE', "%{$search}%")
              ->orWhere('incident_location', 'LIKE', "%{$search}%")
              ->orWhere('victim_name', 'LIKE', "%{$search}%")
              ->orWhere('victim_email', 'LIKE', "%{$search}%");
        });
    }

    // Helper methods
    public function markAsReviewing()
    {
        $this->update(['status' => self::STATUS_REVIEWING]);
    }

    public function markAsResolved($response = null)
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'admin_response' => $response ?? $this->admin_response,
            'responded_at' => now(),
        ]);
    }

    public function markAsDismissed($reason = null)
    {
        $this->update([
            'status' => self::STATUS_DISMISSED,
            'admin_response' => $reason ?? $this->admin_response,
            'responded_at' => now(),
        ]);
    }

    public function addResponse($response, $status = null)
    {
        $data = [
            'admin_response' => $response,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ];

        if ($status && in_array($status, [self::STATUS_REVIEWING, self::STATUS_RESOLVED, self::STATUS_DISMISSED])) {
            $data['status'] = $status;
        }

        $this->update($data);
    }

    // Get statistics helper
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'pending' => self::pending()->count(),
            'reviewing' => self::reviewing()->count(),
            'resolved' => self::resolved()->count(),
            'dismissed' => self::dismissed()->count(),
            'anonymous' => self::anonymous()->count(),
            'physical' => self::byType(self::TYPE_PHYSICAL)->count(),
            'verbal' => self::byType(self::TYPE_VERBAL)->count(),
            'sexual' => self::byType(self::TYPE_SEXUAL)->count(),
            'cyber' => self::byType(self::TYPE_CYBER)->count(),
            'other' => self::byType(self::TYPE_OTHER)->count(),
        ];
    }

    // Get available statuses for dropdowns
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REVIEWING => 'Reviewing',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed',
        ];
    }

    // Get available types for dropdowns
    public static function getTypes()
    {
        return [
            self::TYPE_PHYSICAL => 'Physical',
            self::TYPE_VERBAL => 'Verbal',
            self::TYPE_SEXUAL => 'Sexual',
            self::TYPE_CYBER => 'Cyber',
            self::TYPE_OTHER => 'Other',
        ];
    }
}