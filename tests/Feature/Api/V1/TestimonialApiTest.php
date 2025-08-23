<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TestimonialApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create testimonials
        Testimonial::factory()->count(5)->create(['is_active' => true]);
        Testimonial::factory()->count(2)->create([
            'is_active' => true,
            'is_featured' => true
        ]);
        Testimonial::factory()->count(1)->create(['is_active' => false]); // Inactive
    }

    public function test_can_get_all_active_testimonials()
    {
        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'position',
                             'company',
                             'content',
                             'avatar',
                             'rating',
                             'is_featured',
                             'created_at'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(7, 'data'); // Only active testimonials
    }

    public function test_can_get_featured_testimonials()
    {
        $response = $this->getJson('/api/v1/testimonials/featured');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
        
        $data = $response->json('data');
        
        // All returned records should be featured
        foreach ($data as $testimonial) {
            $this->assertTrue($testimonial['is_featured']);
        }
    }

    public function test_can_limit_testimonials_results()
    {
        $response = $this->getJson('/api/v1/testimonials?limit=3');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }

    public function test_can_limit_featured_testimonials_results()
    {
        $response = $this->getJson('/api/v1/testimonials/featured?limit=1');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_testimonials_are_ordered_by_created_at_desc()
    {
        // Create testimonials with specific dates
        Testimonial::truncate();
        
        $testimonial1 = Testimonial::factory()->create([
            'is_active' => true,
            'created_at' => now()->subDays(3)
        ]);
        $testimonial2 = Testimonial::factory()->create([
            'is_active' => true,
            'created_at' => now()->subDay()
        ]);
        $testimonial3 = Testimonial::factory()->create([
            'is_active' => true,
            'created_at' => now()->subDays(2)
        ]);

        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by created_at descending (most recent first)
        $this->assertEquals($testimonial2->id, $data[0]['id']);
        $this->assertEquals($testimonial3->id, $data[1]['id']);
        $this->assertEquals($testimonial1->id, $data[2]['id']);
    }

    public function test_inactive_testimonials_are_excluded()
    {
        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Verify no inactive testimonials are in response
        foreach ($data as $testimonial) {
            $this->assertDatabaseHas('testimonials', [
                'id' => $testimonial['id'],
                'is_active' => true
            ]);
        }
    }

    public function test_testimonials_include_rating()
    {
        // Create testimonial with specific rating
        Testimonial::factory()->create([
            'is_active' => true,
            'rating' => 5
        ]);

        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $testimonialWithRating = collect($data)->first(function ($testimonial) {
            return $testimonial['rating'] === 5;
        });
        
        $this->assertNotNull($testimonialWithRating);
        $this->assertEquals(5, $testimonialWithRating['rating']);
    }

    public function test_handles_no_active_testimonials_gracefully()
    {
        Testimonial::query()->update(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Testimonials retrieved successfully'
                 ]);
    }

    public function test_handles_no_featured_testimonials_gracefully()
    {
        Testimonial::query()->update(['is_featured' => false]);
        
        $response = $this->getJson('/api/v1/testimonials/featured');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Featured testimonials retrieved successfully'
                 ]);
    }

    public function test_testimonials_include_company_and_position()
    {
        // Create testimonial with specific company and position
        $company = 'Test Company Inc.';
        $position = 'Senior Developer';
        
        Testimonial::factory()->create([
            'is_active' => true,
            'company' => $company,
            'position' => $position
        ]);

        $response = $this->getJson('/api/v1/testimonials');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $testimonialWithDetails = collect($data)->first(function ($testimonial) use ($company, $position) {
            return $testimonial['company'] === $company && $testimonial['position'] === $position;
        });
        
        $this->assertNotNull($testimonialWithDetails);
        $this->assertEquals($company, $testimonialWithDetails['company']);
        $this->assertEquals($position, $testimonialWithDetails['position']);
    }
}