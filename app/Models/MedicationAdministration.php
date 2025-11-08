<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAdministration extends Model
{
    use HasFactory;

    protected $table = 'medical_administrations'; 

    protected $fillable = [
        'patient_id',
        'medication',
        'dose',
        'route',
        'frequency',
        'comments',
        'time',
    ];

       public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}