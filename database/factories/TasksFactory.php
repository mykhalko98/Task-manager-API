<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tasks>
 */
class TasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'       => fake()->realText(32),
            'deadline'    => fake()->dateTimeBetween('-5 days', '+7 days')->format('Y-m-d'),
            'description' => fake()->text(500),
            'owner_id'    => fake()->numberBetween(1, 10),
            'assignee_id' => fake()->numberBetween(1, 10),
            'status'      => fake()->randomElement(['prepared', 'in_progress', 'in_test', 'done'])
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
