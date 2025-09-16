<?php

// TODO:
/* [x] fillables
   [x] relationships
   [x] connect to cdss
   */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    //relationship sa patient table
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    //relationship sa cdss physical exam table
    public function cdssAssessment()
    {
        return $this->hasOne(CdssPhysicalExam::class);
    }
}