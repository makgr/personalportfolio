<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create services
        Service::factory()->count(5)->create(['is_active' => true]);
        Service::factory()->count(2)->create(['is_active' => false]); // Inactive services
    }

    public function test_can_get_all_active_services()
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'description',
                             'icon',
                             'features',
                             'price_range',
                             'order'
                         ]
                     ],
                     'message'
                 ])
                 ->assertJsonCount(5, 'data'); // Only active services
    }

    public function test_services_are_ordered_correctly()
    {
        // Update services with specific order
        $services = Service::where('is_active', true)->take(3)->get();
        $services[0]->update(['order' => 3]);
        $services[1]->update(['order' => 1]);
        $services[2]->update(['order' => 2]);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        
        // Should be ordered by the 'order' field
        $this->assertEquals(1, $data[0]['order']);
        $this->assertEquals(2, $data[1]['order']);
        $this->assertEquals(3, $data[2]['order']);
    }

    public function test_inactive_services_are_not_included()
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
        
        // Verify no inactive services are in response
        $data = $response->json('data');
        foreach ($data as $service) {
            $this->assertDatabaseHas('services', [
                'id' => $service['id'],
                'is_active' => true
            ]);
        }
    }

    public function test_handles_no_active_services_gracefully()
    {
        Service::query()->update(['is_active' => false]);
        
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Services retrieved successfully'
                 ]);
    }

    public function test_services_include_features_array()
    {
        // Create service with specific features
        $features = ['Feature 1', 'Feature 2', 'Feature 3'];
        Service::factory()->create([
            'is_active' => true,
            'features' => $features
        ]);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $serviceWithFeatures = collect($data)->first(function ($service) use ($features) {
            return $service['features'] === $features;
        });
        
        $this->assertNotNull($serviceWithFeatures);
        $this->assertEquals($features, $serviceWithFeatures['features']);
    }

    public function test_services_include_price_range()
    {
        // Create service with specific price range
        $priceRange = '$500 - $2000';
        Service::factory()->create([
            'is_active' => true,
            'price_range' => $priceRange
        ]);

        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $serviceWithPrice = collect($data)->first(function ($service) use ($priceRange) {
            return $service['price_range'] === $priceRange;
        });
        
        $this->assertNotNull($serviceWithPrice);
        $this->assertEquals($priceRange, $serviceWithPrice['price_range']);
    }
}