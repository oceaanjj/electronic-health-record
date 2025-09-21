<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vitals extends Model
{
    use HasFactory;


    protected $table = 'vital_signs';

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'patient_id',
        'day_no',
        'date',
        'temperature',
        'hr',
        'rr',
        'bp',
        'spo2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
