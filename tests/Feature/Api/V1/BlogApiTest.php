<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class BlogApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user and category
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['type' => 'blog']);
        
        // Create blog posts
        BlogPost::factory()->count(5)->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now()
        ]);
        
        BlogPost::factory()->count(2)->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'is_published' => true,
            'published_at' => now(),
            'is_featured' => true
        ]);
    }

    public function test_can_get_all_blog_posts()
    {
        $response = $this->getJson('/api/v1/blog-posts');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'slug',
                             'excerpt',
                             'featured_image',
                             'published_at',
                             'reading_time',
                             'views_count',
                             'category',
                             'author'
                         ]
                     ],
                     'meta' => [
                         'current_page',
                         'per_page',
                         'total'
                     ]
                 ]);
    }

    public function test_can_get_featured_blog_posts()
    {
        $response = $this->getJson('/api/v1/blog-posts/featured');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_can_get_single_blog_post_by_slug()
    {
        $post = BlogPost::first();
        
        $response = $this->getJson("/api/v1/blog-posts/{$post->slug}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'slug',
                         'content',
                         'excerpt',
                         'featured_image',
                         'published_at',
                         'reading_time',
                         'views_count',
                         'category',
                         'author',
                         'tags'
                     ]
                 ]);
    }

    public function test_blog_post_increments_views()
    {
        $post = BlogPost::first();
        $initialViews = $post->views_count;
        
        $this->getJson("/api/v1/blog-posts/{$post->slug}");
        
        $post->refresh();
        $this->assertEquals($initialViews + 1, $post->views_count);
    }

    public function test_can_search_blog_posts()
    {
        $post = BlogPost::first();
        
        $response = $this->getJson("/api/v1/blog-posts?search=" . substr($post->title, 0, 5));

        $response->assertStatus(200);
    }

    public function test_can_filter_blog_posts_by_category()
    {
        $response = $this->getJson("/api/v1/blog-posts?category={$this->category->slug}");

        $response->assertStatus(200);
    }

    public function test_can_paginate_blog_posts()
    {
        $response = $this->getJson('/api/v1/blog-posts?per_page=3&page=1');

        $response->assertStatus(200)
                 ->assertJsonPath('meta.per_page', 3);
    }
}