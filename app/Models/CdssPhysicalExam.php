<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdssPhysicalExam extends Model
{
    use HasFactory;

    protected $table = 'cdss_physical_exams';

    protected $fillable = [
        'physical_exam_id',
        'patient_id',
        'alerts',
    ];

    public function physicalExam()
    {
        return $this->belongsTo(PhysicalExam::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
