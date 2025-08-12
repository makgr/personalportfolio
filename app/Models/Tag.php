<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tags';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'usage_count',
    ];

    // Relationships
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    public function blogPosts()
    {
        return $this->morphedByMany(BlogPost::class, 'taggable');
    }

    // Scopes
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    // Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function decrementUsage()
    {
        $this->decrement('usage_count');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (!$tag->slug) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }
}
