<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->unique()->slug(),
            'position' => $this->faker->jobTitle(),
            'bio' => $this->faker->paragraphs(2, true),
            'image' => 'team/' . $this->faker->word() . '.jpg',
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'social_links' => [
                'linkedin' => 'https://linkedin.com/in/' . $this->faker->userName(),
                'twitter' => 'https://twitter.com/' . $this->faker->userName(),
                'github' => 'https://github.com/' . $this->faker->userName()
            ],
            'skills' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue.js', 'MySQL', 'JavaScript', 'Python'], 4),
            'experience' => $this->faker->paragraph(),
            'education' => $this->faker->paragraph(),
            'certifications' => $this->faker->randomElements(['AWS Certified', 'Laravel Certified', 'Google Cloud Certified'], 2),
            'is_featured' => false,
            'is_active' => true,
            'order' => $this->faker->numberBetween(1, 100)
        ];
    }

    public function featured(): Factory
    {
        return $this->state([
            'is_featured' => true
        ]);
    }

    public function inactive(): Factory
    {
        return $this->state([
            'is_active' => false
        ]);
    }
}