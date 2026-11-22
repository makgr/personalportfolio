<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPostStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_post_can_be_created_with_an_empty_tags_input(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson(route('blog-posts.store'), [
            'title' => 'Laravel and Next.js',
            'excerpt' => 'Why I split the stack.',
            'content' => 'Body text.',
            'status' => 'draft',
            'tags' => '',
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $this->assertDatabaseHas(BlogPost::class, ['title' => 'Laravel and Next.js']);
    }
}
