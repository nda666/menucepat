<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->words(3, true),
            'start_date' => $this->faker->dateTimeBetween('-10 days')->format('Y-m-d H:i:s'),
            'end_date' => $this->faker->dateTimeBetween('+10 days', '+30days')->format('Y-m-d  H:i:s'),
        ];
    }
}
