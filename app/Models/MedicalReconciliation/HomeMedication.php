<?php

namespace App\Models\MedicalReconciliation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeMedication extends Model
{
    use HasFactory;

    protected $table = 'home_medication';

    protected $fillable = [
        'patient_id',
        'home_med',
        'home_dose',
        'home_route',
        'home_frequency',
        'home_indication',
        'home_text',
        // Support original names too
        'medication',
        'dosage',
        'frequency',
        'route',
        'indication',
    ];
}
