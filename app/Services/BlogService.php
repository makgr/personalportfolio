<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogService
{
    protected MediaService $mediaService;

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function createBlogPost(array $data, $featuredImage = null, array $tags = [])
    {
        // Generate slug if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Handle featured image
        if ($featuredImage) {
            $data['featured_image'] = $this->mediaService->upload($featuredImage, 'blog');
        }

        // Calculate reading time
        $data['reading_time'] = $this->calculateReadingTime($data['content']);

        // Create blog post
        $post = BlogPost::create($data);

        // Attach tags
        if (!empty($tags)) {
            $post->tagModels()->sync($tags);
        }

        // Create initial version
        $post->createVersion('Initial creation');

        return $post;
    }

    public function updateBlogPost(int $id, array $data, $featuredImage = null, array $tags = [])
    {
        $post = BlogPost::findOrFail($id);

        // Update slug if title changed
        if (isset($data['title']) && $data['title'] !== $post->title) {
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = Str::slug($data['title']);
            }
        }

        // Handle featured image
        if ($featuredImage) {
            // Delete old image
            if ($post->featured_image) {
                $this->mediaService->delete($post->featured_image);
            }
            $data['featured_image'] = $this->mediaService->upload($featuredImage, 'blog');
        }

        // Recalculate reading time if content changed
        if (isset($data['content'])) {
            $data['reading_time'] = $this->calculateReadingTime($data['content']);
        }

        // Update post
        $post->update($data);

        // Sync tags
        if (!empty($tags)) {
            $post->tagModels()->sync($tags);
        }

        return $post->fresh();
    }

    public function deleteBlogPost(int $id)
    {
        $post = BlogPost::findOrFail($id);

        // Delete featured image
        if ($post->featured_image) {
            $this->mediaService->delete($post->featured_image);
        }

        // Delete media
        $post->media()->delete();

        // Delete post
        return $post->delete();
    }

    public function publishBlogPost(int $id)
    {
        $post = BlogPost::findOrFail($id);
        $post->update([
            'status' => 'published',
            'published_at' => now()
        ]);
        return $post;
    }

    public function unpublishBlogPost(int $id)
    {
        $post = BlogPost::findOrFail($id);
        $post->update([
            'status' => 'draft',
            'published_at' => null
        ]);
        return $post;
    }

    public function scheduleBlogPost(int $id, $scheduledAt)
    {
        $post = BlogPost::findOrFail($id);
        $post->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt
        ]);
        return $post;
    }

    protected function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200)); // 200 words per minute
    }
}
