<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportsIssues extends Model
{
     use HasFactory;

    protected $fillable = [
        'username', 'issue_date', 'description', 'user_id', 'title', 'type', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
