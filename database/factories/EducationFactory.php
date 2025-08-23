<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

class EducationFactory extends Factory
{
    protected $model = Education::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-10 years', '-2 years');
        $endDate = $this->faker->boolean(80) ? $this->faker->dateTimeBetween($startDate, 'now') : null;

        return [
            'institution' => $this->faker->company() . ' University',
            'degree' => $this->faker->randomElement([
                'Bachelor of Science',
                'Master of Science', 
                'Bachelor of Arts',
                'Master of Arts',
                'PhD'
            ]),
            'field_of_study' => $this->faker->randomElement([
                'Computer Science',
                'Software Engineering',
                'Information Technology',
                'Web Development',
                'Data Science'
            ]),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
            'description' => $this->faker->paragraph(),
            'grade' => $this->faker->randomElement(['3.8 GPA', '3.5 GPA', 'First Class', 'Upper Second Class', 'Magna Cum Laude']),
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr()
        ];
    }

    public function ongoing(): Factory
    {
        return $this->state([
            'end_date' => null
        ]);
    }
}