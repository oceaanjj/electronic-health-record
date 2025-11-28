<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IntakeAndOutput extends Model
{
    protected $table = 'intake_and_outputs';
    protected $fillable = [
        'patient_id',
        'day_no',
        'oral_intake',
        'iv_fluids_volume',
        'iv_fluids_type',
        'urine_output',
        'alert',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
    public function nursingDiagnoses()
    {
        return $this->hasMany(NursingDiagnosis::class, 'intake_and_output_id');
    }
}
