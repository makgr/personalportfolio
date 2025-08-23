<?php

namespace Database\Factories;

use App\Models\ContactSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactSubmissionFactory extends Factory
{
    protected $model = ContactSubmission::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraphs(2, true),
            'phone' => $this->faker->phoneNumber(),
            'is_read' => false,
            'replied_at' => null
        ];
    }

    public function read(): Factory
    {
        return $this->state([
            'is_read' => true
        ]);
    }

    public function replied(): Factory
    {
        return $this->state([
            'is_read' => true,
            'replied_at' => $this->faker->dateTimeBetween('-1 month', 'now')
        ]);
    }
}