<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_can_be_updated_with_an_empty_feature_input(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::create([
            'title' => 'Sellora',
            'slug' => 'sellora',
            'short_description' => 'POS system.',
            'description' => 'POS and inventory system.',
            'type' => 'web',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->putJson(route('projects.update', $project->id), [
            'title' => 'Sellora POS',
            'short_description' => 'POS system.',
            'description' => 'POS and inventory system.',
            'type' => 'web',
            'status' => 'published',
            'tech_stack' => '',
            'features' => [''],
        ], ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertOk();
        $this->assertSame('Sellora POS', $project->fresh()->title);
    }
}
