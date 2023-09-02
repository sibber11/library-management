<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //todo: add genre_id
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'price' => $this->faker->numberBetween(100, 1000),
            'available' => 1,
            'description' => $this->faker->paragraph,
            'published_at' => $this->faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'created_at' => $this->faker->dateTimeBetween('-1 years', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
