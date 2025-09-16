<?php
// TODO:
// [x] fillables
// [x] connect to physical exam
// [x] connect to patient
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdssPhysicalExam extends Model
{
    use HasFactory;

    protected $table = 'cdss_physical_exam';

    protected $fillable = [
        'physical_exam_id',
        'patient_id',
        'alerts',
        'risk_level',
        'requires_immediate_attention',
        'abnormal_findings',
        'triggered_rules'
    ];

    // cast to make sure na boolean lang laman ng requires_immediate_attention
    protected $casts = [
        'requires_immediate_attention' => 'boolean',
    ];

    // connect sa phyiscal exam
    public function physicalExam()
    {
        return $this->belongsTo(PhysicalExam::class);
    }

    // connect sa patient table
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
