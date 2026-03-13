<?php

namespace App\Models\MedicalReconciliation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangesInMedication extends Model
{
    use HasFactory;

    protected $table = 'changes_in_medication';

    protected $fillable = [
        'patient_id',
        'change_med',
        'change_dose',
        'change_route',
        'change_frequency',
        'change_text',
        // Support original names too
        'medication',
        'change',
        'reason',
    ];
}
