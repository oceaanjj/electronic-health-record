<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientRegistrationTest extends TestCase
{
    // use RefreshDatabase; 

    public function test_can_register_patient_with_emergency_contacts()
    {
        // Create a user to authenticate
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('patients.store'), [
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'middle_name' => 'M',
            'age' => 30,
            'birthdate' => '1995-01-01',
            'sex' => 'Male',
            'address' => '123 Test St',
            'birthplace' => 'Test City',
            'religion' => 'Test Religion',
            'ethnicity' => 'Test Ethnicity',
            'chief_complaints' => 'Pain',
            'admission_date' => '2025-01-01',
            'room_no' => '101',
            'bed_no' => 'A',
            
            // Array inputs as expected from the form
            'contact_name' => ['Emergency One', 'Emergency Two'],
            'contact_relationship' => ['Father', 'Mother'],
            'contact_number' => ['09123456789', '09987654321'],
        ]);

        $response->assertRedirect(route('patients.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('patients', [
            'first_name' => 'Test',
            'last_name' => 'Patient',
        ]);

        $patient = \App\Models\Patient::where('first_name', 'Test')->first();

        $this->assertNotNull($patient->contact_name);
        $this->assertIsArray($patient->contact_name);
        $this->assertEquals(['Emergency One', 'Emergency Two'], $patient->contact_name);
    }
}
