<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shell_renders_skeletons_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('ob-skeleton', false);
    }

    public function test_local_assets_carry_their_file_modified_time_as_version(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $version = filemtime(public_path('assets/css/obsidian.css'));

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertSee(asset('assets/css/obsidian.css').'?v='.$version, false);
    }

    public function test_dashboard_data_returns_sections_and_chart(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        // forceCreate: ContactSubmissionFactory sets an is_read column the table does not have
        ContactSubmission::forceCreate([
            'name' => 'Rahim Uddin',
            'email' => 'rahim@example.com',
            'subject' => 'Project inquiry',
            'message' => 'Hello',
            'status' => 'new',
        ]);

        $response = $this->actingAs($admin)->getJson(route('home.data'));

        $response->assertOk()
            ->assertJsonStructure(['stats', 'projects', 'messages', 'chart' => ['labels', 'messages', 'visits']])
            ->assertJsonCount(30, 'chart.labels');
        $this->assertStringContainsString('Rahim Uddin', $response->json('messages'));
        $this->assertStringContainsString('<span class="ob-pill">1 new</span>', $response->json('stats'));
        $this->assertSame(1, array_sum($response->json('chart.messages')));
    }

    public function test_dashboard_data_requires_login(): void
    {
        $this->getJson(route('home.data'))->assertUnauthorized();
    }
}
