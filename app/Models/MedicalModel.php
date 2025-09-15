<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    protected $table = 'medical_history';
    protected $fillable = [
        'condition_name',
        'condition_description',
        'medication_name',
        'medication_dosage',
        'side_effects',
        'medication_comments',
    ];
}

class DevelopmentalHistory extends Model
{
    protected $table = 'development_history';
    protected $fillable = [
        'gross_motor',
        'fine_motor',
        'language',
        'cognitive',
        'social',
    ];
}