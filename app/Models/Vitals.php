<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vitals extends Model
{
    use HasFactory;

    protected $table = 'vital_signs';

    protected $fillable = [
        'patient_id',
        'date',
        'time',
        'day_no',
        'temperature',
        'hr',
        'rr',
        'bp',
        'spo2',
        'alerts',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // FOR ADPIE:
    public function nursingDiagnoses()
    {
        return $this->hasOne(NursingDiagnosis::class);
    }
}