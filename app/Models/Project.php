<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $guarded = [];

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'category_id',
        'featured_image',
        'images',
        'type',
        'status',
        'client_name',
        'project_date',
        'project_url',
        'github_url',
        'demo_url',
        'tech_stack',
        'features',
        'challenges',
        'solutions',
        'duration_weeks',
        'team_size',
        'my_role',
        'is_featured',
        'order',
        'views',
        'meta_tags',
        'published_at',
    ];

    protected $casts = [
        'images' => 'array',
        'tech_stack' => 'array',
        'features' => 'array',
        'challenges' => 'array',
        'solutions' => 'array',
        'meta_tags' => 'array',
        'is_featured' => 'boolean',
        'project_date' => 'date',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function versions()
    {
        return $this->hasMany(ProjectVersion::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function createVersion($changedBy = null, $notes = null)
    {
        $versionNumber = $this->versions()->max('version_number') + 1;

        return $this->versions()->create([
            'version_number' => $versionNumber,
            'data' => $this->toArray(),
            'changed_by' => $changedBy,
            'change_notes' => $notes,
        ]);
    }

    // Boot method for auto-versioning
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($project) {
            if ($project->isDirty()) {
                $project->createVersion();
            }
        });
    }
}
