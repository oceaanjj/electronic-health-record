<?php

namespace App\Models\MedicalHistory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

class Vaccination extends Model
{
    protected $table = 'vaccination';
    protected $primaryKey = 'patient_id';
    protected $fillable = [
        'patient_id',
        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}