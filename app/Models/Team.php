<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'position',
        'department',
        'bio',
        'email',
        'phone',
        'image',
        'linkedin',
        'twitter',
        'github',
        'website',
        'skills',
        'join_date',
        'employment_type',
        'location',
        'is_featured',
        'is_active',
        'order',
        'icon',
        'color',
    ];

    protected $casts = [
        'skills' => 'array',
        'join_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'join_date',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
        });

        static::updating(function ($team) {
            if ($team->isDirty('name') && empty($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
        });
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

    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('join_date', 'desc');
    }

    // Accessors
    public function getEmploymentTypeBadgeColorAttribute()
    {
        return match ($this->employment_type) {
            'full_time' => 'success',
            'part_time' => 'primary',
            'contract' => 'warning',
            'intern' => 'info',
            default => 'secondary'
        };
    }

    public function getDepartmentBadgeColorAttribute()
    {
        return match ($this->department) {
            'engineering' => 'primary',
            'design' => 'info',
            'marketing' => 'warning',
            'sales' => 'success',
            'hr' => 'secondary',
            'management' => 'dark',
            default => 'light'
        };
    }

    public function getFormattedJoinDateAttribute()
    {
        if (! $this->join_date) {
            return 'N/A';
        }

        return $this->join_date->format('M d, Y');
    }

    public function getTenureAttribute()
    {
        if (! $this->join_date) {
            return 'N/A';
        }

        $diff = $this->join_date->diffForHumans(null, true, false, 2);

        return $diff;
    }

    public function getSocialLinksAttribute()
    {
        $links = [];

        if ($this->linkedin) {
            $links['linkedin'] = $this->linkedin;
        }
        if ($this->twitter) {
            $links['twitter'] = $this->twitter;
        }
        if ($this->github) {
            $links['github'] = $this->github;
        }
        if ($this->website) {
            $links['website'] = $this->website;
        }

        return $links;
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1).substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }
}
