<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'skills';

    protected $fillable = [
        'name',
        'slug',
        'category',
        'proficiency',
        'years_experience',
        'icon',
        'color',
        'description',
        'certifications',
        'is_primary',
        'order',
        'is_active',
    ];

    protected $casts = [
        'certifications' => 'array',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeByProficiency($query)
    {
        return $query->orderBy('proficiency', 'desc');
    }
}
