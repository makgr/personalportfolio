<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user and profile for testing
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
    }

    public function test_can_get_profile_information()
    {
        $response = $this->getJson('/api/v1/profile');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'first_name',
                         'last_name',
                         'email',
                         'phone',
                         'bio',
                         'location',
                         'website',
                         'avatar',
                         'resume_url',
                         'social_links',
                         'skills',
                         'experiences',
                         'education'
                     ],
                     'message'
                 ]);
    }

    public function test_profile_endpoint_handles_missing_profile_gracefully()
    {
        // Delete all profiles
        Profile::truncate();
        
        $response = $this->getJson('/api/v1/profile');
        
        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Profile not found'
                 ]);
    }
}