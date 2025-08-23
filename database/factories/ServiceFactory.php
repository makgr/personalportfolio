<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'icon' => 'bx bx-' . $this->faker->word(),
            'features' => $this->faker->randomElements([
                'Responsive Design',
                'SEO Optimization', 
                'Cross-browser Compatibility',
                'Mobile First Approach',
                'Performance Optimization',
                'Security Best Practices'
            ], 3),
            'price_range' => '$' . $this->faker->numberBetween(500, 2000) . ' - $' . $this->faker->numberBetween(2001, 5000),
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
}