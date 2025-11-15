<?php

namespace Database\Factories;
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{

    public function definition(): array
    {
        $complaints = [
            'Fever and chills',
            'Cough with phlegm',
            'Shortness of breath',
            'Chest pain',
            'Headache',
            'Abdominal pain',
            'Nausea and vomiting',
            'Dizziness',
            'Sore throat',
            'Back pain',
            'Diarrhea',
            'Fatigue',
            'Skin rash',
            'Joint pain',
            'Swelling of legs',
            'Loss of appetite',
            'Urinary frequency',
            'Blurred vision',
            'Palpitations',
            'Weight loss',
        ];

        $nurse_id = User::where('role', 'Nurse')->inRandomOrder()->first()?->id ?? 1;

        return [

            'user_id' => $nurse_id,


            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->firstName(),
            'age' => $this->faker->numberBetween(1, 42),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address(),
            'birthplace' => $this->faker->city(),
            'birthdate' => $this->faker->date('Y-m-d'),
            'religion' => $this->faker->randomElement(['Catholic', 'Iglesia ni Cristo', 'Christian']),
            'ethnicity' => $this->faker->randomElement(['Filipino', 'Foreign']),
            'chief_complaints' => $this->faker->randomElement($complaints),
            'admission_date' => $this->faker->date('Y-m-d'),
            'room_no' => $this->faker->buildingNumber(),
            'bed_no' => $this->faker->bothify('##?'),
            'contact_name' => $this->faker->name(),
            'contact_relationship' => $this->faker->randomElement(['Parent', 'Sibling', 'Spouse', 'Child', 'Friend']),
            'contact_number' => $this->faker->phoneNumber(),

        ];
    }
}
