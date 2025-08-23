<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'position' => $this->faker->jobTitle(),
            'company' => $this->faker->company(),
            'content' => $this->faker->paragraphs(2, true),
            'avatar' => 'testimonials/' . $this->faker->word() . '.jpg',
            'rating' => $this->faker->numberBetween(4, 5),
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