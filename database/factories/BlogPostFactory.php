<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->unique()->slug(),
            'excerpt' => $this->faker->paragraph(),
            'content' => $this->faker->paragraphs(5, true),
            'featured_image' => 'blog/' . $this->faker->word() . '.jpg',
            'author_id' => User::factory(),
            'category_id' => Category::factory()->blog(),
            'is_published' => true,
            'is_featured' => false,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'reading_time' => $this->faker->numberBetween(3, 15),
            'views_count' => $this->faker->numberBetween(0, 2000),
            'tags' => $this->faker->randomElements(['Laravel', 'PHP', 'JavaScript', 'Vue.js', 'MySQL', 'CSS', 'HTML'], 3)
        ];
    }

    public function featured(): Factory
    {
        return $this->state([
            'is_featured' => true
        ]);
    }

    public function unpublished(): Factory
    {
        return $this->state([
            'is_published' => false,
            'published_at' => null
        ]);
    }

    public function published(): Factory
    {
        return $this->state([
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ]);
    }
}