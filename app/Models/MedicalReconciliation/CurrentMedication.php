<?php

namespace App\Models\MedicalReconciliation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMedication extends Model
{
    use HasFactory;

    protected $table = 'current_medication';

    protected $fillable = [
        'patient_id',
        'current_med',
        'current_dose',
        'current_route',
        'current_frequency',
        'current_indication',
        'current_text',
        // Support original names too
        'medication',
        'dosage',
        'frequency',
        'route',
        'indication',
    ];
}
