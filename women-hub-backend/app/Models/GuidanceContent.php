<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuidanceContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'title',
        'body',
        'photo',
        'category',
        'status',
        'language',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return asset('storage/' . $this->photo);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUnpublished($query)
    {
        return $query->where('status', 'unpublished');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
