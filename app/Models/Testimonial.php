<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'testimonials';

    protected $fillable = [
        'client_name',
        'client_position',
        'client_company',
        'content',
        'rating',
        'client_image',
        'client_linkedin',
        'project_id',
        'is_featured',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeWithHighRating($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }
}
