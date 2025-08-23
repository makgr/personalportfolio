<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EducationApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create education records
        Education::factory()->count(3)->create();
    }

    public function test_can_get_all_education_records()
    {
        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'institution',
                             'degree',
                             'field_of_study',
                             'start_date',
                             'end_date',
                             'description',
                             'grade',
                             'location'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(3, 'data');
    }

    public function test_education_records_are_ordered_by_start_date_desc()
    {
        // Create education records with specific dates
        Education::truncate();
        
        Education::factory()->create(['start_date' => '2020-01-01']);
        Education::factory()->create(['start_date' => '2022-01-01']);
        Education::factory()->create(['start_date' => '2021-01-01']);

        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by start_date descending (most recent first)
        $this->assertEquals('2022-01-01', $data[0]['start_date']);
        $this->assertEquals('2021-01-01', $data[1]['start_date']);
        $this->assertEquals('2020-01-01', $data[2]['start_date']);
    }

    public function test_handles_no_education_records_gracefully()
    {
        Education::truncate();
        
        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Education records retrieved successfully'
                 ]);
    }

    public function test_education_includes_all_required_fields()
    {
        // Create specific education record
        $education = Education::factory()->create([
            'institution' => 'Test University',
            'degree' => 'Bachelor of Science',
            'field_of_study' => 'Computer Science',
            'grade' => '3.8 GPA',
            'location' => 'Test City, TC',
            'description' => 'Test description'
        ]);

        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $testEducation = collect($data)->first(function ($edu) {
            return $edu['institution'] === 'Test University';
        });
        
        $this->assertNotNull($testEducation);
        $this->assertEquals('Test University', $testEducation['institution']);
        $this->assertEquals('Bachelor of Science', $testEducation['degree']);
        $this->assertEquals('Computer Science', $testEducation['field_of_study']);
        $this->assertEquals('3.8 GPA', $testEducation['grade']);
        $this->assertEquals('Test City, TC', $testEducation['location']);
        $this->assertEquals('Test description', $testEducation['description']);
    }

    public function test_education_dates_are_properly_formatted()
    {
        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        foreach ($data as $education) {
            // Start date should be properly formatted
            if ($education['start_date']) {
                $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $education['start_date']);
            }
            
            // End date should be properly formatted if present
            if ($education['end_date']) {
                $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $education['end_date']);
            }
        }
    }

    public function test_education_can_have_null_end_date_for_ongoing()
    {
        // Create ongoing education
        Education::factory()->create([
            'start_date' => '2023-01-01',
            'end_date' => null
        ]);

        $response = $this->getJson('/api/v1/education');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $ongoingEducation = collect($data)->first(function ($edu) {
            return $edu['end_date'] === null && $edu['start_date'] === '2023-01-01';
        });
        
        $this->assertNotNull($ongoingEducation);
        $this->assertNull($ongoingEducation['end_date']);
    }
}