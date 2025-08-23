<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(3, true),
            'image' => 'projects/' . $this->faker->word() . '.jpg',
            'url' => $this->faker->url(),
            'github_url' => 'https://github.com/' . $this->faker->userName() . '/' . $this->faker->word(),
            'technologies' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue.js', 'MySQL', 'JavaScript', 'CSS'], 3),
            'category_id' => Category::factory(),
            'is_published' => true,
            'is_featured' => false,
            'order' => $this->faker->numberBetween(1, 100),
            'views_count' => $this->faker->numberBetween(0, 1000)
        ];
    }

    public function featured(): Factory
    {
        return $this->state([
            'is_featured' => true
        ]);
    }

    public function unpublished(): Factory
    {
        return $this->state([
            'is_published' => false
        ]);
    }
}