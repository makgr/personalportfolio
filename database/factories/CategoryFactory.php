<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'type' => $this->faker->randomElement(['project', 'blog']),
            'description' => $this->faker->sentence(),
            'color' => $this->faker->hexColor(),
            'icon' => 'bx bx-' . $this->faker->word(),
            'order' => $this->faker->numberBetween(1, 100),
            'is_active' => true
        ];
    }

    public function inactive(): Factory
    {
        return $this->state([
            'is_active' => false
        ]);
    }

    public function project(): Factory
    {
        return $this->state([
            'type' => 'project'
        ]);
    }

    public function blog(): Factory
    {
        return $this->state([
            'type' => 'blog'
        ]);
    }
}