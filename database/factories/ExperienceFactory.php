<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-5 years', '-1 year');
        $endDate = $this->faker->boolean(70) ? $this->faker->dateTimeBetween($startDate, 'now') : null;

        return [
            'company' => $this->faker->company(),
            'position' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraphs(2, true),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'is_current' => $endDate === null,
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'company_website' => $this->faker->url(),
            'technologies' => $this->faker->randomElements(['PHP', 'Laravel', 'Vue.js', 'MySQL', 'JavaScript', 'Python', 'React'], 4)
        ];
    }

    public function current(): Factory
    {
        return $this->state([
            'end_date' => null,
            'is_current' => true
        ]);
    }

    public function past(): Factory
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-5 years', '-1 year');
            $endDate = $this->faker->dateTimeBetween($startDate, '-6 months');
            
            return [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'is_current' => false
            ];
        });
    }
}