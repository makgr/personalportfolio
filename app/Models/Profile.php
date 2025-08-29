<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $table = 'profiles';

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
        'years_of_experience',
        'total_projects',
        'happy_clients',
        'hero_image_url',
        'resume_url',
        'social_links',
        'meta_tags',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'meta_tags' => 'array',
        'is_active' => 'boolean',
        'years_of_experience' => 'integer',
        'total_projects' => 'integer',
        'happy_clients' => 'integer',
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
