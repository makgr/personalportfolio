<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Training extends Model
{
    use HasFactory;

    protected $table = 'training';

    protected $fillable = [
        'title',
        'slug',
        'provider',
        'type',
        'category',
        'description',
        'certificate_url',
        'credential_id',
        'start_date',
        'end_date',
        'duration_hours',
        'status',
        'score',
        'skills_gained',
        'instructor',
        'rating',
        'notes',
        'icon',
        'color',
        'order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'skills_gained' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'rating' => 'decimal:1',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    // Boot method to auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($training) {
            if (empty($training->slug)) {
                $training->slug = Str::slug($training->title);
            }
        });

        static::updating(function ($training) {
            if ($training->isDirty('title') && empty($training->slug)) {
                $training->slug = Str::slug($training->title);
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('start_date', 'desc');
    }

    public function scopeByDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('start_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('end_date', '<=', $endDate);
        }
        return $query;
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_hours) {
            return 'N/A';
        }

        if ($this->duration_hours < 24) {
            return $this->duration_hours . ' hours';
        } else {
            $days = floor($this->duration_hours / 24);
            $remainingHours = $this->duration_hours % 24;
            return $days . ' days' . ($remainingHours > 0 ? ', ' . $remainingHours . ' hours' : '');
        }
    }

    public function getStatusBadgeColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'primary',
            'planned' => 'warning',
            default => 'secondary'
        };
    }

    public function getCategoryBadgeColorAttribute()
    {
        return match($this->category) {
            'technical' => 'info',
            'soft_skills' => 'success',
            'business' => 'warning',
            'design' => 'danger',
            'other' => 'secondary',
            default => 'secondary'
        };
    }

    public function getTypeBadgeColorAttribute()
    {
        return match($this->type) {
            'certification' => 'success',
            'course' => 'primary',
            'workshop' => 'info',
            'bootcamp' => 'warning',
            'degree' => 'danger',
            default => 'secondary'
        };
    }

    public function getRatingStarsAttribute()
    {
        if (!$this->rating) {
            return '';
        }

        $stars = '';
        $fullStars = floor($this->rating);
        $hasHalfStar = ($this->rating - $fullStars) >= 0.5;

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $fullStars) {
                $stars .= '<i class="bx bxs-star text-warning"></i>';
            } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                $stars .= '<i class="bx bxs-star-half text-warning"></i>';
            } else {
                $stars .= '<i class="bx bx-star text-muted"></i>';
            }
        }

        return $stars;
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isPlanned()
    {
        return $this->status === 'planned';
    }

    public function hasCertificate()
    {
        return !empty($this->certificate_url);
    }

    public function hasCredential()
    {
        return !empty($this->credential_id);
    }
}