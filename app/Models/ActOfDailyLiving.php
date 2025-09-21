<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActOfDailyLiving extends Model
{
    use HasFactory;

    protected $table = 'act_of_daily_living';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'day_no',
        'date',
        'mobility_assessment',
        'hygiene_assessment',
        'toileting_assessment',
        'feeding_assessment',
        'hydration_assessment',
        'sleep_pattern_assessment',
        'pain_level_assessment',
        'alerts'
    ];

    /**
     * Get the patient that owns the Activities of Daily Living record.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
