<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'bio' => $this->faker->paragraphs(2, true),
            'location' => $this->faker->city() . ', ' . $this->faker->stateAbbr(),
            'website' => $this->faker->url(),
            'avatar' => 'avatars/' . $this->faker->word() . '.jpg',
            'resume_url' => 'resumes/resume.pdf',
            'social_links' => [
                'linkedin' => 'https://linkedin.com/in/' . $this->faker->userName(),
                'twitter' => 'https://twitter.com/' . $this->faker->userName(),
                'github' => 'https://github.com/' . $this->faker->userName()
            ]
        ];
    }
}