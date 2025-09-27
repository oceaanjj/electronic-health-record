<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vitals extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vital_signs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}