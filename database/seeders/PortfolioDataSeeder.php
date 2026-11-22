<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the real portfolio content exported to database/seeders/data/*.json.
 * Rows keep their original ids so relations stay intact; re-running updates them instead of duplicating.
 */
class PortfolioDataSeeder extends Seeder
{
    /**
     * Tables in foreign-key order.
     *
     * @var array<int, string>
     */
    private array $tables = [
        'users', 'profiles', 'settings', 'seo_settings', 'categories', 'tags', 'skills', 'services',
        'experiences', 'education', 'training', 'certifications', 'teams', 'projects', 'testimonials',
        'blog_posts', 'taggables', 'media',
    ];

    public function run(): void
    {
        foreach ($this->tables as $table) {
            $rows = json_decode(file_get_contents(database_path("seeders/data/{$table}.json")), true);

            if ($rows === []) {
                continue;
            }

            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table($table)->upsert($chunk, ['id']);
            }
        }
    }
}
