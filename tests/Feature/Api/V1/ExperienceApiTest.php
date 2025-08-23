<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ExperienceApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create experiences with different dates
        Experience::factory()->create([
            'start_date' => '2023-01-01',
            'end_date' => null, // Current job
            'is_current' => true
        ]);
        
        Experience::factory()->create([
            'start_date' => '2022-01-01',
            'end_date' => '2022-12-31',
            'is_current' => false
        ]);
        
        Experience::factory()->create([
            'start_date' => '2021-01-01',
            'end_date' => '2021-12-31',
            'is_current' => false
        ]);
    }

    public function test_can_get_all_experiences()
    {
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'company',
                             'position',
                             'description',
                             'start_date',
                             'end_date',
                             'is_current',
                             'location',
                             'company_website',
                             'technologies'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(3, 'data');
    }

    public function test_experiences_are_ordered_by_start_date_desc()
    {
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by start_date descending (most recent first)
        $this->assertEquals('2023-01-01', $data[0]['start_date']);
        $this->assertEquals('2022-01-01', $data[1]['start_date']);
        $this->assertEquals('2021-01-01', $data[2]['start_date']);
    }

    public function test_current_experience_shows_null_end_date()
    {
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $currentExperience = collect($data)->first(function ($exp) {
            return $exp['is_current'] === true;
        });
        
        $this->assertNotNull($currentExperience);
        $this->assertNull($currentExperience['end_date']);
        $this->assertTrue($currentExperience['is_current']);
    }

    public function test_past_experiences_have_end_dates()
    {
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $pastExperiences = collect($data)->filter(function ($exp) {
            return $exp['is_current'] === false;
        });
        
        $this->assertCount(2, $pastExperiences);
        
        foreach ($pastExperiences as $experience) {
            $this->assertNotNull($experience['end_date']);
            $this->assertFalse($experience['is_current']);
        }
    }

    public function test_experiences_include_technologies_array()
    {
        // Update an experience with specific technologies
        $technologies = ['PHP', 'Laravel', 'Vue.js', 'MySQL'];
        Experience::first()->update(['technologies' => $technologies]);

        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $experienceWithTech = collect($data)->first(function ($exp) use ($technologies) {
            return $exp['technologies'] === $technologies;
        });
        
        $this->assertNotNull($experienceWithTech);
        $this->assertEquals($technologies, $experienceWithTech['technologies']);
    }

    public function test_handles_no_experiences_gracefully()
    {
        Experience::truncate();
        
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Experiences retrieved successfully'
                 ]);
    }

    public function test_experience_dates_are_properly_formatted()
    {
        $response = $this->getJson('/api/v1/experiences');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        foreach ($data as $experience) {
            // Start date should always be present and properly formatted
            $this->assertNotNull($experience['start_date']);
            $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $experience['start_date']);
            
            // End date can be null (for current positions) or properly formatted
            if ($experience['end_date'] !== null) {
                $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $experience['end_date']);
            }
        }
    }
}