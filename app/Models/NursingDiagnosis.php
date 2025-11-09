<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingDiagnosis extends Model
{
    protected $fillable = [
        'physical_exam_id',
        'intake_and_output_id',
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
}