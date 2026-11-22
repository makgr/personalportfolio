<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_without_new_image_keeps_existing_image(): void
    {
        $team = Team::forceCreate(['name' => 'Jane Doe', 'slug' => 'jane-doe', 'position' => 'Developer', 'image' => 'teams/existing.jpg']);

        $result = app(TeamService::class)->updateTeam($team->id, [
            'name' => 'Updated Name',
            'position' => $team->position,
            'employment_type' => 'full_time',
            'image' => null,
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('teams/existing.jpg', $team->fresh()->image);
        $this->assertSame('Updated Name', $team->fresh()->name);
    }
}
