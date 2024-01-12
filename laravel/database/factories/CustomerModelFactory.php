<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'phone_number' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date(),
        ];
    }
}
