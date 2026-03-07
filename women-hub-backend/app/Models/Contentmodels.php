<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HygieneArticle extends Model
{
    protected $fillable = ['title', 'content', 'category', 'image_url', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];
}

class GeneralGuide extends Model
{
    protected $fillable = ['title', 'content', 'category', 'icon', 'is_published'];
    protected $casts = ['is_published' => 'boolean'];
}

class EmergencyContact extends Model
{
    protected $fillable = ['name', 'phone', 'type', 'region', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}