<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Admin
        User::updateOrCreate(['username' => 'admin'], [
            'email'      => 'admin@example.com',
            'password'   => Hash::make('admin'),
            'role'       => 'admin',
            'full_name'  => 'System Administrator',
            'birthdate'  => '1985-01-01',
            'age'        => 41,
            'sex'        => 'Male',
            'address'    => '123 Tech Ave, Silicon Valley',
            'birthplace' => 'New York City',
        ]);

        // Sample Doctor
        User::updateOrCreate(['username' => 'doctor'], [
            'email'      => 'doctor@example.com',
            'password'   => Hash::make('doctor'),
            'role'       => 'doctor',
            'full_name'  => 'Dr. Gregory House',
            'birthdate'  => '1970-05-15',
            'age'        => 55,
            'sex'        => 'Male',
            'address'    => 'Princeton-Plainsboro Teaching Hospital',
            'birthplace' => 'Chicago',
        ]);

        // Sample Nurse
        User::updateOrCreate(['username' => 'nurse'], [
            'email'      => 'nurse@example.com',
            'password'   => Hash::make('nurse'),
            'role'       => 'nurse',
            'full_name'  => 'Nurse Florence Nightingale',
            'birthdate'  => '1995-12-10',
            'age'        => 30,
            'sex'        => 'Female',
            'address'    => '456 Care St, Health City',
            'birthplace' => 'Florence, Italy',
        ]);
    }
}
