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
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
            'role' => 'admin',
        ]);

        // Sample Doctor
        User::create([
            'username' => 'doctor',
            'email' => 'doctor@example.com',
            'password' => Hash::make('doctor'),
            'role' => 'doctor',
        ]);

        // Sample Nurse
        User::create([
            'username' => 'nurse',
            'email' => 'nurse@example.com',
            'password' => Hash::make('nurse'),
            'role' => 'nurse',
        ]);
    }
}
