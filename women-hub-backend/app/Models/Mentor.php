<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'photo', 'bio',
        'area_of_support', 'available_days',
        'available_time_from', 'available_time_to', 'status',
    ];

    protected $casts = [
        'available_days' => 'array',
    ];

    public function getAreaLabelAttribute(): string
    {
        return match($this->area_of_support) {
            'menstrual_hygiene' => 'Menstrual Hygiene',
            'general_issues'    => 'General Issues',
            'both'              => 'Menstrual Hygiene & General Issues',
            default             => ucfirst($this->area_of_support),
        };
    }

    public function getAvailabilityStringAttribute(): string
    {
        $days = implode(', ', $this->available_days ?? []);
        return "{$days} · {$this->available_time_from} – {$this->available_time_to}";
    }
}