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
        'mobility_alert',
        'hygiene_alert',
        'toileting_alert',
        'feeding_alert',
        'hydration_alert',
        'sleep_pattern_alert',
        'pain_level_alert',
    ];

    /**
     * Get the patient that owns the Activities of Daily Living record.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'patient_id');
    }
}
