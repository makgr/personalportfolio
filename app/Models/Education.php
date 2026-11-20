<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'education';

    protected $fillable = [
        'institution',
        'degree',
        'field_of_study',
        'description',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'grade',
        'activities',
        'courses',
        'institution_logo',
        'certificate_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'activities' => 'array',
        'courses' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')
            ->orderBy('start_date', 'desc');
    }
}
