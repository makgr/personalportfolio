<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'name' => $this->faker->name(),
            'is_subscribed' => true,
            'subscribed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'unsubscribed_at' => null
        ];
    }

    public function unsubscribed(): Factory
    {
        return $this->state(function (array $attributes) {
            $unsubscribedAt = $this->faker->dateTimeBetween($attributes['subscribed_at'], 'now');
            
            return [
                'is_subscribed' => false,
                'unsubscribed_at' => $unsubscribedAt
            ];
        });
    }
}