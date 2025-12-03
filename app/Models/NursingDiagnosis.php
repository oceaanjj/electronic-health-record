<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingDiagnosis extends Model
{
    protected $fillable = [
        'physical_exam_id',
        'intake_and_output_id',
        'lab_values_id', // Added for Lab Values
        'adl_id', // Added for Act of Daily Living
        'vital_signs_id',
        'patient_id',
        'diagnosis',
        'planning',
        'intervention',
        'evaluation',

        // ===== START OF CHANGE =====
        // Add the new alert columns
        'diagnosis_alert',
        'planning_alert',
        'intervention_alert',
        'evaluation_alert',
        'rule_file_path',
        // ===== END OF CHANGE =====
    ];

    public function physicalExam()
    {
        return $this->belongsTo(PhysicalExam::class);
    }
    public function intakeAndOutput()
    {
        return $this->belongsTo(IntakeAndOutput::class);
    }

    public function labValues()
    {
        return $this->belongsTo(LabValues::class);
    }

    public function actOfDailyLiving() // New relationship
    {
        return $this->belongsTo(ActOfDailyLiving::class, 'adl_id');
    }

    public function vitalSigns()
    {
        return $this->belongsTo(Vitals::class, 'vital_signs_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}