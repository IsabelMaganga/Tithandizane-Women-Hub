<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HarassmentReport extends Model
{
    protected $fillable = [
        'user_id', 'incident_type', 'description', 'location',
        'incident_date', 'perpetrator_info', 'is_anonymous', 'status', 'admin_notes',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'incident_date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
}