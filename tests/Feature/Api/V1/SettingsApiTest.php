<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class SettingsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create public settings
        Setting::create([
            'key' => 'site_name',
            'value' => 'My Portfolio',
            'type' => 'text',
            'is_public' => true
        ]);
        
        Setting::create([
            'key' => 'contact_email',
            'value' => 'contact@example.com',
            'type' => 'email',
            'is_public' => true
        ]);
        
        Setting::create([
            'key' => 'social_links',
            'value' => json_encode([
                'twitter' => 'https://twitter.com/example',
                'linkedin' => 'https://linkedin.com/in/example'
            ]),
            'type' => 'json',
            'is_public' => true
        ]);
        
        // Create private setting
        Setting::create([
            'key' => 'admin_password',
            'value' => 'secret',
            'type' => 'password',
            'is_public' => false
        ]);
    }

    public function test_can_get_all_public_settings()
    {
        $response = $this->getJson('/api/v1/settings');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'site_name',
                         'contact_email',
                         'social_links'
                     ],
                     'message'
                 ]);
        
        $data = $response->json('data');
        
        $this->assertEquals('My Portfolio', $data['site_name']);
        $this->assertEquals('contact@example.com', $data['contact_email']);
        $this->assertIsArray($data['social_links']);
        $this->assertArrayNotHasKey('admin_password', $data);
    }

    public function test_can_get_single_public_setting_by_key()
    {
        $response = $this->getJson('/api/v1/settings/site_name');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'key' => 'site_name',
                         'value' => 'My Portfolio',
                         'type' => 'text'
                     ],
                     'message' => 'Setting retrieved successfully'
                 ]);
    }

    public function test_returns_404_for_non_existent_setting()
    {
        $response = $this->getJson('/api/v1/settings/non_existent_key');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Setting not found'
                 ]);
    }

    public function test_returns_404_for_private_setting()
    {
        $response = $this->getJson('/api/v1/settings/admin_password');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Setting not found'
                 ]);
    }

    public function test_json_settings_are_properly_decoded()
    {
        $response = $this->getJson('/api/v1/settings/social_links');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertIsArray($data['value']);
        $this->assertArrayHasKey('twitter', $data['value']);
        $this->assertArrayHasKey('linkedin', $data['value']);
    }

    public function test_handles_no_public_settings_gracefully()
    {
        Setting::query()->update(['is_public' => false]);
        
        $response = $this->getJson('/api/v1/settings');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [],
                     'message' => 'Settings retrieved successfully'
                 ]);
    }

    public function test_settings_exclude_sensitive_information()
    {
        // Create settings with potentially sensitive keys
        Setting::create([
            'key' => 'mail_password',
            'value' => 'secret_mail_password',
            'type' => 'password',
            'is_public' => true // Even if marked public, should be filtered
        ]);

        $response = $this->getJson('/api/v1/settings');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertArrayNotHasKey('mail_password', $data);
    }

    public function test_boolean_settings_are_properly_cast()
    {
        Setting::create([
            'key' => 'maintenance_mode',
            'value' => '1',
            'type' => 'boolean',
            'is_public' => true
        ]);

        $response = $this->getJson('/api/v1/settings/maintenance_mode');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertTrue($data['value']);
        $this->assertIsBool($data['value']);
    }

    public function test_integer_settings_are_properly_cast()
    {
        Setting::create([
            'key' => 'posts_per_page',
            'value' => '10',
            'type' => 'integer',
            'is_public' => true
        ]);

        $response = $this->getJson('/api/v1/settings/posts_per_page');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertEquals(10, $data['value']);
        $this->assertIsInt($data['value']);
    }

    public function test_array_settings_are_properly_handled()
    {
        Setting::create([
            'key' => 'featured_services',
            'value' => json_encode(['web-development', 'mobile-development']),
            'type' => 'array',
            'is_public' => true
        ]);

        $response = $this->getJson('/api/v1/settings/featured_services');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertIsArray($data['value']);
        $this->assertContains('web-development', $data['value']);
        $this->assertContains('mobile-development', $data['value']);
    }
}