<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalModel extends Model
{
    protected $table = 'medical_history';
    protected $fillable = [
        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment',
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