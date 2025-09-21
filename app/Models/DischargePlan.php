<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DischargePlan extends Model
{
    protected $table = 'discharge_planning';
    protected $fillable = [
        'patient_id',
        'criteria_feverRes',
        'criteria_patientCount',
        'criteria_manageFever',
        'criteria_manageFever2',
        'instruction_med',
        'instruction_appointment',
        'instruction_fluidIntake',
        'instruction_exposure',
        'instruction_complications',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
