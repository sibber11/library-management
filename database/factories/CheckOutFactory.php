<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CheckOut>
 */
class CheckOutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'book_id' => \App\Models\Book::factory(),
            'member_id' => \App\Models\Member::factory(),
            'check_out_date' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
            'due_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'check_in_date' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['check_out_date'], '+1 month');
            },
            'is_checked_in' => $this->faker->boolean,
        ];
    }
}
