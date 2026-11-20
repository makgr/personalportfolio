<?php

namespace App\Models;

use App\Traits\HasSeo;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BlogPost extends Model
{
    use \App\Models\Concerns\DisplaysInSiteTimezone, HasFactory, HasSeo, Sluggable;

    protected $table = 'blog_posts';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'author_id',
        'featured_image',
        'status',
        'reading_time',
        'tags',
        'meta_tags',
        'is_featured',
        'allow_comments',
        'views',
        'likes',
        'published_at',
        'scheduled_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'meta_tags' => 'array',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    /**
     * The schedule form sends local wall-clock time; read it in the site timezone so it is stored as UTC.
     */
    public function setScheduledAtAttribute(mixed $value): void
    {
        if (is_string($value) && $value !== '') {
            $value = Carbon::parse($value, config('app.display_timezone'));
        }

        $this->attributes['scheduled_at'] = $this->fromDateTime($value);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function versions()
    {
        return $this->hasMany(BlogPostVersion::class);
    }

    public function tagModels()
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

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled')
            ->whereNotNull('scheduled_at');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeWithComments($query)
    {
        return $query->where('allow_comments', true);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementLikes()
    {
        $this->increment('likes');
    }

    public function calculateReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $minutes = ceil($wordCount / 200); // Assuming 200 words per minute

        return $minutes;
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
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

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (! $post->reading_time) {
                $post->reading_time = $post->calculateReadingTime();
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('content')) {
                $post->reading_time = $post->calculateReadingTime();
            }

            if ($post->isDirty() && $post->exists) {
                $post->createVersion();
            }
        });
    }
}
