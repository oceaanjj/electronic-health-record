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
            'username'   => $this->faker->unique()->userName(),
            'role'       => $this->faker->randomElement(['Admin', 'Doctor', 'Nurse']),
            'email'      => $this->faker->unique()->safeEmail(),
            'password'   => static::$password ??= Hash::make('password'),
            'full_name'  => $this->faker->name(),
            'birthdate'  => $this->faker->date(),
            'age'        => $this->faker->numberBetween(25, 65),
            'sex'        => $this->faker->randomElement(['Male', 'Female']),
            'address'    => $this->faker->address(),
            'birthplace' => $this->faker->city(),
        ];
    }
}
