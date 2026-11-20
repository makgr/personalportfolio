<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\ContactSubmission;
use App\Models\Experience;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteTimezoneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.display_timezone' => 'Asia/Dhaka']);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_timestamps_are_stored_in_utc_and_shown_in_site_timezone(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-11-20 20:30:00', 'UTC'));

        $message = ContactSubmission::forceCreate([
            'name' => 'Rahim',
            'email' => 'rahim@example.com',
            'subject' => 'Hi',
            'message' => 'Hello',
        ]);

        $this->assertSame('2026-11-20 20:30:00', DB::table('contact_submissions')->value('created_at'));
        $this->assertSame('2026-11-21 02:30', $message->fresh()->created_at->format('Y-m-d H:i'));
    }

    public function test_resaving_a_model_keeps_its_timestamps_in_utc(): void
    {
        $message = ContactSubmission::forceCreate([
            'name' => 'Rahim',
            'email' => 'rahim@example.com',
            'subject' => 'Hi',
            'message' => 'Hello',
            'replied_at' => '2026-01-01 10:00:00',
        ]);

        $message = $message->fresh();
        $message->replied_at = $message->replied_at; // Asia/Dhaka instance
        $message->save();

        $this->assertSame('2026-01-01 10:00:00', DB::table('contact_submissions')->value('replied_at'));
    }

    public function test_date_only_columns_do_not_shift(): void
    {
        config(['app.display_timezone' => 'America/New_York']);

        $experience = Experience::forceCreate([
            'company_name' => 'Acme',
            'position' => 'Developer',
            'description' => 'Work',
            'start_date' => '2024-01-15',
        ]);

        $this->assertSame('2024-01-15', $experience->fresh()->start_date->toDateString());
    }

    public function test_scheduled_at_form_input_is_read_as_site_time(): void
    {
        $post = new BlogPost;
        $post->scheduled_at = '2026-12-01T09:00';

        $this->assertSame('2026-12-01 03:00:00', $post->getAttributes()['scheduled_at']);
    }

    public function test_admin_can_save_timezone_setting(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('settings.update'), ['timezone' => 'Europe/London'])
            ->assertRedirect(route('settings.index'));

        $this->assertSame('Europe/London', Setting::get('timezone'));
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('settings.update'), ['timezone' => 'Mars/Olympus'])
            ->assertSessionHasErrors('timezone');

        $this->assertNull(Setting::get('timezone'));
    }
}
