<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start',
        'end',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'type',
        'status',
        'color',
        'created_by',
        'max_participants',
        'current_participants'
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(\App\Models\User::class, 'event_participants')
            ->withPivot('registered_at')
            ->withTimestamps();
    }
}
