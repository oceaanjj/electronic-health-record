<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NursingDiagnosis extends Model
{
    protected $fillable = [
        'physical_exam_id',
        'diagnosis',
        'planning',
        'intervention',
        'evaluation',
    ];

    public function physicalExam()
    {
        return $this->belongsTo(PhysicalExam::class);
    }
}
