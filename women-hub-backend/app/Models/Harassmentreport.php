<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarassmentReport extends Model
{
    protected $fillable = [
        'reference_number', 'reporter_name', 'reporter_contact',
        'is_anonymous', 'incident_type', 'incident_date',
        'incident_location', 'description', 'perpetrator_info',
        'status', 'admin_notes', 'assigned_to',
    ];

    protected $casts = [
        'is_anonymous'  => 'boolean',
        'incident_date' => 'date',
    ];

    public function assignedAdmin()
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'new'          => '<span class="badge bg-danger">New</span>',
            'under_review' => '<span class="badge bg-warning text-dark">Under Review</span>',
            'resolved'     => '<span class="badge bg-success">Resolved</span>',
            'closed'       => '<span class="badge bg-secondary">Closed</span>',
            default        => '<span class="badge bg-light text-dark">'.ucfirst($this->status).'</span>',
        };
    }

    public static function generateReference(): string
    {
        $year  = date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'RPT-'.$year.'-'.str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}