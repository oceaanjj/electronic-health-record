<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\patientsFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Patient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Generate test users
        $this->call([
            UserSeeder::class,

        ]);

        //Generate 20 patients
        Patient::factory(20)->create();


    }
}
