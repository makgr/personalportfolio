<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BlogPostVersion extends Model
{
    use HasFactory;

    protected $table = 'blog_post_versions';

    protected $fillable = [
        'blog_post_id',
        'version_number',
        'data',
        'changed_by',
        'change_notes',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Relationships
    public function blogPost()
    {
        return $this->belongsTo(BlogPost::class);
    }

    // Methods
    public function restore()
    {
        $blogPost = $this->blogPost;
        $data = $this->data;

        unset($data['id'], $data['created_at'], $data['updated_at']);

        $blogPost->update($data);

        return $blogPost;
    }
}
