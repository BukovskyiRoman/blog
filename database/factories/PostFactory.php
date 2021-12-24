<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text('25'),
            'body' => $this->faker->text('200'),
            'user_id' => $this->faker->biasedNumberBetween(1, 20),
            'created_at'=> now(),
        ];
    }
}
