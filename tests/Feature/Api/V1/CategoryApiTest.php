<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Project;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create categories
        Category::factory()->count(3)->create([
            'type' => 'project',
            'is_active' => true
        ]);
        
        Category::factory()->count(2)->create([
            'type' => 'blog',
            'is_active' => true
        ]);
        
        // Create inactive category
        Category::factory()->create([
            'type' => 'project',
            'is_active' => false
        ]);
    }

    public function test_can_get_all_active_categories()
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'slug',
                             'type',
                             'description',
                             'color',
                             'icon',
                             'order'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(5, 'data'); // Only active categories
    }

    public function test_can_filter_categories_by_type()
    {
        $response = $this->getJson('/api/v1/categories?type=project');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_get_categories_with_counts()
    {
        // Create some projects and blog posts
        $projectCategory = Category::where('type', 'project')->first();
        $blogCategory = Category::where('type', 'blog')->first();
        $user = User::factory()->create();
        
        Project::factory()->count(2)->create(['category_id' => $projectCategory->id]);
        BlogPost::factory()->count(3)->create([
            'category_id' => $blogCategory->id,
            'author_id' => $user->id
        ]);
        
        $response = $this->getJson('/api/v1/categories?with_counts=1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'projects_count',
                             'blog_posts_count'
                         ]
                     ]
                 ]);
    }

    public function test_can_get_categories_by_type_endpoint()
    {
        $response = $this->getJson('/api/v1/categories/type/project');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_returns_error_for_invalid_category_type()
    {
        $response = $this->getJson('/api/v1/categories/type/invalid');

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Invalid category type'
                 ]);
    }

    public function test_can_get_single_category_by_slug()
    {
        $category = Category::first();
        
        $response = $this->getJson("/api/v1/categories/{$category->slug}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'name',
                         'slug',
                         'type',
                         'description',
                         'color',
                         'icon',
                         'order',
                         'projects_count',
                         'blog_posts_count'
                     ],
                     'message'
                 ]);
    }

    public function test_returns_404_for_non_existent_category()
    {
        $response = $this->getJson('/api/v1/categories/non-existent-slug');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Category not found'
                 ]);
    }

    public function test_returns_404_for_inactive_category()
    {
        $inactiveCategory = Category::where('is_active', false)->first();
        
        $response = $this->getJson("/api/v1/categories/{$inactiveCategory->slug}");

        $response->assertStatus(404);
    }
}