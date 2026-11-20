<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $table = 'experiences';

    protected $fillable = [
        'company_name',
        'position',
        'employment_type',
        'location',
        'description',
        'responsibilities',
        'achievements',
        'technologies',
        'start_date',
        'end_date',
        'is_current',
        'company_logo',
        'company_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'responsibilities' => 'array',
        'achievements' => 'array',
        'technologies' => 'array',
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

    // Accessors
    public function getDurationAttribute()
    {
        $start = $this->start_date;
        $end = $this->is_current ? now() : $this->end_date;

        if (! $start || ! $end) {
            return null;
        }

        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0) {
            return $years.' year'.($years > 1 ? 's' : '').
                ($months > 0 ? ' '.$months.' month'.($months > 1 ? 's' : '') : '');
        }

        return $months.' month'.($months > 1 ? 's' : '');
    }
}
