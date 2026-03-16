<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralGuide extends Model
{
    protected $fillable = [
        'title',
        'content'
    ];
}