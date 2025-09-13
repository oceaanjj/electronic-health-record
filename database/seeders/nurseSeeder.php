<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NurseModel;
use Illuminate\Support\Facades\Hash;

class nurseSeeder extends Seeder {
    public function run() {
        nurseModel::create([
            'nurse_id' => '1001',
            'name'     => 'Test Nurse',
            'password' => Hash::make('password321'),
        ]);
    }
}
