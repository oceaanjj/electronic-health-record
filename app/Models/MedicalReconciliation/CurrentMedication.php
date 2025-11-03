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
        'medication',
        'dosage',
        'frequency',
        'route',
        'indication',
    ];
}
