<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AccountDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->optional()->numerify('#########'),
            'birthdate' => $this->faker->optional()->date(),
            'union_date' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'location' => $this->faker->optional()->city(),
            'biography' => $this->faker->optional()->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
