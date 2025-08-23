<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Training;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TrainingApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create training records
        Training::factory()->count(5)->create(['is_active' => true]);
        Training::factory()->count(2)->create([
            'is_active' => true,
            'is_featured' => true
        ]);
        Training::factory()->count(1)->create(['is_active' => false]); // Inactive
    }

    public function test_can_get_all_active_training_records()
    {
        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'description',
                             'provider',
                             'completion_date',
                             'certificate_url',
                             'skills',
                             'duration',
                             'is_featured'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(7, 'data'); // Only active training records
    }

    public function test_can_get_featured_training_records()
    {
        $response = $this->getJson('/api/v1/training/featured');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
        
        $data = $response->json('data');
        
        // All returned records should be featured
        foreach ($data as $training) {
            $this->assertTrue($training['is_featured']);
        }
    }

    public function test_can_limit_featured_training_results()
    {
        $response = $this->getJson('/api/v1/training/featured?limit=1');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_training_records_are_ordered_by_completion_date_desc()
    {
        // Create training with specific dates
        Training::truncate();
        
        Training::factory()->create([
            'completion_date' => '2023-01-01',
            'is_active' => true
        ]);
        Training::factory()->create([
            'completion_date' => '2023-06-01',
            'is_active' => true
        ]);
        Training::factory()->create([
            'completion_date' => '2023-03-01',
            'is_active' => true
        ]);

        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by completion_date descending (most recent first)
        $this->assertEquals('2023-06-01', $data[0]['completion_date']);
        $this->assertEquals('2023-03-01', $data[1]['completion_date']);
        $this->assertEquals('2023-01-01', $data[2]['completion_date']);
    }

    public function test_inactive_training_records_are_excluded()
    {
        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Verify no inactive training records are in response
        foreach ($data as $training) {
            $this->assertDatabaseHas('trainings', [
                'id' => $training['id'],
                'is_active' => true
            ]);
        }
    }

    public function test_training_includes_skills_array()
    {
        // Create training with specific skills
        $skills = ['Laravel', 'PHP', 'Database Design'];
        Training::factory()->create([
            'is_active' => true,
            'skills' => $skills
        ]);

        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $trainingWithSkills = collect($data)->first(function ($training) use ($skills) {
            return $training['skills'] === $skills;
        });
        
        $this->assertNotNull($trainingWithSkills);
        $this->assertEquals($skills, $trainingWithSkills['skills']);
    }

    public function test_handles_no_active_training_gracefully()
    {
        Training::query()->update(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Training records retrieved successfully'
                 ]);
    }

    public function test_handles_no_featured_training_gracefully()
    {
        Training::query()->update(['is_featured' => false]);
        
        $response = $this->getJson('/api/v1/training/featured');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Featured training records retrieved successfully'
                 ]);
    }

    public function test_training_completion_dates_are_properly_formatted()
    {
        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        foreach ($data as $training) {
            if ($training['completion_date']) {
                $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $training['completion_date']);
            }
        }
    }

    public function test_training_includes_certificate_url()
    {
        // Create training with certificate URL
        $certificateUrl = 'https://example.com/certificate.pdf';
        Training::factory()->create([
            'is_active' => true,
            'certificate_url' => $certificateUrl
        ]);

        $response = $this->getJson('/api/v1/training');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $trainingWithCert = collect($data)->first(function ($training) use ($certificateUrl) {
            return $training['certificate_url'] === $certificateUrl;
        });
        
        $this->assertNotNull($trainingWithCert);
        $this->assertEquals($certificateUrl, $trainingWithCert['certificate_url']);
    }
}