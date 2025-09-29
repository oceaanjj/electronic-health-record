<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PresentIllness extends Model
{
    protected $table = 'present_illness';
    protected $fillable = [
        'patient_id',
        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
}








