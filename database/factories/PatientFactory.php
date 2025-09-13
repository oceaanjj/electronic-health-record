<?php

namespace Database\Factories;

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

        return [
            'name' => $this->faker->name(),
            'age' => $this->faker->numberBetween(0, 12),
            'sex' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'address' => $this->faker->address(),
            'birthplace' => $this->faker->city(),
            'religion' => $this->faker->randomElement(['Catholic', 'Iglesia ni Cristo', 'Christian']),
            'ethnicity' => $this->faker->randomElement(['Filipino', 'Foreign']),
            'chief_complaints' => $this->faker->randomElement($complaints),
            'admission_date' => $this->faker->date('Y-m-d'),
        ];
    }
}
