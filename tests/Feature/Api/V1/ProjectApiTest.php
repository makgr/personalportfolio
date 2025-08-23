<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create categories
        $this->category = Category::factory()->create(['type' => 'project']);
        
        // Create projects
        Project::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'is_published' => true
        ]);
        
        Project::factory()->count(2)->create([
            'category_id' => $this->category->id,
            'is_published' => true,
            'is_featured' => true
        ]);
    }

    public function test_can_get_all_projects()
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'slug',
                             'description',
                             'image',
                             'url',
                             'github_url',
                             'technologies',
                             'category',
                             'created_at'
                         ]
                     ]
                 ]);
    }

    public function test_can_get_featured_projects()
    {
        $response = $this->getJson('/api/v1/projects/featured');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_can_get_single_project_by_slug()
    {
        $project = Project::first();
        
        $response = $this->getJson("/api/v1/projects/{$project->slug}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'slug',
                         'description',
                         'content',
                         'image',
                         'url',
                         'github_url',
                         'technologies',
                         'category'
                     ]
                 ]);
    }

    public function test_returns_404_for_non_existent_project()
    {
        $response = $this->getJson('/api/v1/projects/non-existent-slug');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Project not found'
                 ]);
    }

    public function test_can_filter_projects_by_category()
    {
        $response = $this->getJson("/api/v1/projects?category={$this->category->slug}");

        $response->assertStatus(200);
    }

    public function test_can_limit_projects_results()
    {
        $response = $this->getJson('/api/v1/projects?limit=3');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}