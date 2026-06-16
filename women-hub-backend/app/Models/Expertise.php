<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expertise extends Model
{
    protected $fillable = ['name', 'slug'];

    public function mentors()
    {
        return $this->belongsToMany(User::class, 'expertise_user')
                    ->where('role', 'mentor')
                    ->where('status', 'active');
    }
}