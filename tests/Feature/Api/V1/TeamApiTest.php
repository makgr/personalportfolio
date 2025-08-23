<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TeamApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create team members
        Team::factory()->count(5)->create(['is_active' => true]);
        Team::factory()->count(2)->create([
            'is_active' => true,
            'is_featured' => true
        ]);
        Team::factory()->count(1)->create(['is_active' => false]); // Inactive
    }

    public function test_can_get_all_active_team_members()
    {
        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'slug',
                             'position',
                             'bio',
                             'image',
                             'email',
                             'phone',
                             'social_links',
                             'skills',
                             'is_featured',
                             'order'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(7, 'data'); // Only active team members
    }

    public function test_can_get_featured_team_members()
    {
        $response = $this->getJson('/api/v1/teams/featured');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
        
        $data = $response->json('data');
        
        // All returned records should be featured
        foreach ($data as $teamMember) {
            $this->assertTrue($teamMember['is_featured']);
        }
    }

    public function test_can_get_single_team_member_by_slug()
    {
        $teamMember = Team::where('is_active', true)->first();
        
        $response = $this->getJson("/api/v1/teams/{$teamMember->slug}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'name',
                         'slug',
                         'position',
                         'bio',
                         'image',
                         'email',
                         'phone',
                         'social_links',
                         'skills',
                         'experience',
                         'education',
                         'certifications',
                         'is_featured'
                     ],
                     'message'
                 ]);
    }

    public function test_returns_404_for_non_existent_team_member()
    {
        $response = $this->getJson('/api/v1/teams/non-existent-slug');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Team member not found'
                 ]);
    }

    public function test_returns_404_for_inactive_team_member()
    {
        $inactiveTeamMember = Team::where('is_active', false)->first();
        
        $response = $this->getJson("/api/v1/teams/{$inactiveTeamMember->slug}");

        $response->assertStatus(404);
    }

    public function test_can_limit_team_results()
    {
        $response = $this->getJson('/api/v1/teams?limit=3');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_limit_featured_team_results()
    {
        $response = $this->getJson('/api/v1/teams/featured?limit=1');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_team_members_are_ordered_correctly()
    {
        // Update team members with specific order
        $teamMembers = Team::where('is_active', true)->take(3)->get();
        $teamMembers[0]->update(['order' => 3]);
        $teamMembers[1]->update(['order' => 1]);
        $teamMembers[2]->update(['order' => 2]);

        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by the 'order' field
        $orderedTeamMembers = collect($data)->take(3);
        $this->assertEquals(1, $orderedTeamMembers[0]['order']);
        $this->assertEquals(2, $orderedTeamMembers[1]['order']);
        $this->assertEquals(3, $orderedTeamMembers[2]['order']);
    }

    public function test_inactive_team_members_are_excluded()
    {
        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Verify no inactive team members are in response
        foreach ($data as $teamMember) {
            $this->assertDatabaseHas('teams', [
                'id' => $teamMember['id'],
                'is_active' => true
            ]);
        }
    }

    public function test_team_members_include_social_links_array()
    {
        // Create team member with specific social links
        $socialLinks = [
            'linkedin' => 'https://linkedin.com/in/testuser',
            'twitter' => 'https://twitter.com/testuser',
            'github' => 'https://github.com/testuser'
        ];
        
        Team::factory()->create([
            'is_active' => true,
            'social_links' => $socialLinks
        ]);

        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $teamMemberWithSocial = collect($data)->first(function ($member) use ($socialLinks) {
            return $member['social_links'] === $socialLinks;
        });
        
        $this->assertNotNull($teamMemberWithSocial);
        $this->assertEquals($socialLinks, $teamMemberWithSocial['social_links']);
    }

    public function test_team_members_include_skills_array()
    {
        // Create team member with specific skills
        $skills = ['PHP', 'Laravel', 'Vue.js', 'MySQL'];
        
        Team::factory()->create([
            'is_active' => true,
            'skills' => $skills
        ]);

        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $teamMemberWithSkills = collect($data)->first(function ($member) use ($skills) {
            return $member['skills'] === $skills;
        });
        
        $this->assertNotNull($teamMemberWithSkills);
        $this->assertEquals($skills, $teamMemberWithSkills['skills']);
    }

    public function test_handles_no_active_team_members_gracefully()
    {
        Team::query()->update(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Team members retrieved successfully'
                 ]);
    }

    public function test_handles_no_featured_team_members_gracefully()
    {
        Team::query()->update(['is_featured' => false]);
        
        $response = $this->getJson('/api/v1/teams/featured');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Featured team members retrieved successfully'
                 ]);
    }
}