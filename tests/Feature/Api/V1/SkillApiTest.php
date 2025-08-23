<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SkillApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create skills with different categories
        Skill::factory()->count(3)->create(['category' => 'Programming']);
        Skill::factory()->count(2)->create(['category' => 'Design']);
        Skill::factory()->count(2)->create(['category' => 'Tools']);
    }

    public function test_can_get_all_skills()
    {
        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'Programming' => [
                                 '*' => [
                                     'id',
                                     'name',
                                     'category',
                                     'proficiency',
                                     'icon',
                                     'color',
                                     'order'
                                 ]
                             ]
                         ]
                     ],
                     'message'
                 ]);
    }

    public function test_skills_are_grouped_by_category()
    {
        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        $this->assertArrayHasKey('Programming', $data);
        $this->assertArrayHasKey('Design', $data);
        $this->assertArrayHasKey('Tools', $data);
        
        $this->assertCount(3, $data['Programming']);
        $this->assertCount(2, $data['Design']);
        $this->assertCount(2, $data['Tools']);
    }

    public function test_skills_are_ordered_correctly()
    {
        // Update some skills with specific order
        $skills = Skill::where('category', 'Programming')->take(3)->get();
        $skills[0]->update(['order' => 3]);
        $skills[1]->update(['order' => 1]);
        $skills[2]->update(['order' => 2]);

        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200);
        
        $programmingSkills = $response->json('data.Programming');
        
        // Should be ordered by the 'order' field
        $this->assertEquals(1, $programmingSkills[0]['order']);
        $this->assertEquals(2, $programmingSkills[1]['order']);
        $this->assertEquals(3, $programmingSkills[2]['order']);
    }

    public function test_can_filter_skills_by_category()
    {
        $response = $this->getJson('/api/v1/skills?category=Programming');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        $this->assertArrayHasKey('Programming', $data);
        $this->assertArrayNotHasKey('Design', $data);
        $this->assertArrayNotHasKey('Tools', $data);
    }

    public function test_handles_empty_skills_gracefully()
    {
        Skill::truncate();
        
        $response = $this->getJson('/api/v1/skills');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Skills retrieved successfully'
                 ]);
    }

    public function test_skills_include_correct_proficiency_levels()
    {
        // Create skill with specific proficiency
        Skill::factory()->create([
            'category' => 'Testing',
            'proficiency' => 85
        ]);

        $response = $this->getJson('/api/v1/skills?category=Testing');

        $response->assertStatus(200);
        
        $testingSkills = $response->json('data.Testing');
        $this->assertEquals(85, $testingSkills[0]['proficiency']);
    }
}