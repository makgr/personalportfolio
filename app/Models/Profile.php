<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';
    protected $guarded = [];
    protected $fillable = [
        'full_name',
        'title',
        'subtitle',
        'bio',
        'short_bio',
        'email',
        'phone',
        'location',
        'avatar_url',
        'resume_url',
        'social_links',
        'meta_tags',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'meta_tags' => 'array',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getSocialLinksAttribute($value)
    {
        return json_decode($value, true) ?: [];
    }

    // Get the active profile (usually just one)
    public static function getActive()
    {
        return static::active()->first();
    }
}
