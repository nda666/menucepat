<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nama' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'tgl_lahir' => $this->faker->date(),
            'kota_lahir' => $this->faker->city(),
            'divisi' => $this->faker->jobTitle(),
            'subdivisi' => $this->faker->jobTitle(),
            'company' => $this->faker->company(),
            'department' => $this->faker->company(),
            'jabatan' => $this->faker->jobTitle(),
            'lokasi' => $this->faker->city(),
            'bagian' => $this->faker->jobTitle(),
            'sex' => $this->faker->numberBetween(0, 1),
            'blood' => $this->faker->numberBetween(0, 3),
            'alamat' => $this->faker->address(),
            'nik' => strtoupper(str_replace('-', '', $this->faker->uuid())),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'avatar' => 'public/avatars/J6zKEFHVMxZIARFhN55cy18NMMlXBeFL6xhhygqK.png',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
