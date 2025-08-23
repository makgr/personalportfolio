<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'provider' => $this->faker->randomElement(['Udemy', 'Coursera', 'LinkedIn Learning', 'Pluralsight', 'edX']),
            'completion_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'certificate_url' => $this->faker->url() . '/certificate.pdf',
            'skills' => $this->faker->randomElements(['Laravel', 'PHP', 'Vue.js', 'JavaScript', 'MySQL', 'AWS', 'Docker'], 3),
            'duration' => $this->faker->numberBetween(10, 120) . ' hours',
            'is_featured' => false,
            'is_active' => true
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