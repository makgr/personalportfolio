<?php

namespace Tests\Feature;

use Database\Seeders\PortfolioDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PortfolioDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_loads_every_exported_row_and_can_be_rerun(): void
    {
        $this->seed(PortfolioDataSeeder::class);
        $this->seed(PortfolioDataSeeder::class);

        foreach (glob(database_path('seeders/data/*.json')) as $file) {
            $table = basename($file, '.json');
            $expected = count(json_decode(file_get_contents($file), true));

            $this->assertSame($expected, DB::table($table)->count(), "Row count mismatch for {$table}");
        }
    }
}
