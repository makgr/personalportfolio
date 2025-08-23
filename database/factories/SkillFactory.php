<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

class SkillFactory extends Factory
{
    protected $model = Skill::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'category' => $this->faker->randomElement(['Programming', 'Design', 'Tools', 'Database']),
            'proficiency' => $this->faker->numberBetween(60, 100),
            'icon' => 'skill-' . $this->faker->word() . '.png',
            'color' => $this->faker->hexColor(),
            'order' => $this->faker->numberBetween(1, 100)
        ];
    }

    public function programming(): Factory
    {
        return $this->state([
            'category' => 'Programming'
        ]);
    }

    public function design(): Factory
    {
        return $this->state([
            'category' => 'Design'
        ]);
    }
}