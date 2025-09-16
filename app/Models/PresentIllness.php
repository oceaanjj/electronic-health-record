<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentIllness extends Model
{
    protected $table = 'present_illness';
    protected $fillable = [

        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
}

class PastMedicalSurgical extends Model
{
    protected $table = 'past_medical_surgical';
    protected $fillable = [

        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
}

class Allergy extends Model
{
    protected $table = 'allergies';
    protected $fillable = [

        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
}

class Vaccination extends Model
{
    protected $table = 'vaccination';
    protected $fillable = [
        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
}

class DevelopmentalHistory extends Model
{
    protected $table = 'developmental_history';
    protected $fillable = [
        'gross_motor',
        'fine_motor',
        'language',
        'cognitive',
        'social'
    ];
}