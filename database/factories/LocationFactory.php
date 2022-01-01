<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->address(),
            'latitude' => $this->faker->latitude(),
            'longtitude' => $this->faker->longitude(),
            'radius' => rand(50, 100)
        ];
    }
}
