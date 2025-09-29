<?php

// TODO:
/* [x] fillables
   [x] relationships
   [x] connect to cdss
   */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\PhysicalExamCdssService;

class PhysicalExam extends Model
{
    use HasFactory;

    protected $table = 'physical_exams';

    protected $fillable = [
        'patient_id',
        'general_appearance',
        'skin_condition',
        'eye_condition',
        'oral_condition',
        'cardiovascular',
        'abdomen_condition',
        'extremities',
        'neurological',
    ];

    // Relationship sa patient table
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Connection to nursing diagnosis table
    public function nursingDiagnosis()
    {
        return $this->hasMany(NursingDiagnosis::class);
    }

    // Relationship sa CDSS physical exam table (optional for later use)
    // public function cdssAssessment()
    // {
    //     return $this->hasOne(CdssPhysicalExam::class);
    // }
}
