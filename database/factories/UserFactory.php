<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [

            'name' => $this->faker->name(),
            'role' => $this->faker->randomElement(['Admin', 'Doctor', 'Nurse']),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
        ];
    }


}
