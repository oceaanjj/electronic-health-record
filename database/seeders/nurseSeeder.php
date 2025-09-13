<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class NurseSeeder extends Seeder
{
    public function run(): void
    {
        // Sample Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'Admin',
        ]);

        // Sample Doctor
        User::create([
            'name' => 'Doctor User',
            'email' => 'doctor@example.com',
            'password' => Hash::make('doctor123'),
            'role' => 'Doctor',
        ]);

        // Sample Nurse
        User::create([
            'name' => 'Nurse User',
            'email' => 'nurse@example.com',
            'password' => Hash::make('passwords321'),
            'role' => 'Nurse',
        ]);
    }
}
