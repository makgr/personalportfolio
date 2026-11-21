<?php

// Author: Mohammad Ali Khan

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\FrontendRevalidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FrontendRevalidateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.frontend.url' => 'https://frontend.test/', 'services.frontend.revalidate_secret' => 's3cret']);
        Http::fake(['frontend.test/*' => Http::response(['ok' => true])]);
    }

    public function test_saving_a_model_sends_its_tags_once_with_the_secret(): void
    {
        Setting::create(['key' => 'maintenance_mode', 'value' => '1', 'type' => 'boolean', 'group' => 'site']);
        Setting::create(['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general']);

        app(FrontendRevalidator::class)->flush();

        Http::assertSentCount(1);
        Http::assertSent(fn (Request $r) => $r->url() === 'https://frontend.test/api/revalidate'
            && $r->header('x-revalidate-secret') === ['s3cret']
            && $r['tags'] === ['settings', 'profile']);
    }

    public function test_nothing_is_sent_when_not_configured(): void
    {
        config(['services.frontend.url' => null]);

        Setting::create(['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general']);
        app(FrontendRevalidator::class)->flush();

        Http::assertNothingSent();
    }

    public function test_a_failing_frontend_does_not_break_the_save(): void
    {
        Http::fake(['frontend.test/*' => Http::response('down', 500)]);

        $setting = Setting::create(['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general']);
        app(FrontendRevalidator::class)->flush();

        $this->assertDatabaseHas('settings', ['id' => $setting->id]);
    }
}
