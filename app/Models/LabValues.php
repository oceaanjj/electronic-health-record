<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabValues extends Model
{
    use HasFactory;


    protected $fillable = [
        'patient_id',
        'record_date',
        'wbc_result',
        'wbc_normal_range',
        'rbc_result',
        'rbc_normal_range',
        'hgb_result',
        'hgb_normal_range',
        'hct_result',
        'hct_normal_range',
        'platelets_result',
        'platelets_normal_range',
        'mcv_result',
        'mcv_normal_range',
        'mch_result',
        'mch_normal_range',
        'mchc_result',
        'mchc_normal_range',
        'rdw_result',
        'rdw_normal_range',
        'neutrophils_result',
        'neutrophils_normal_range',
        'lymphocytes_result',
        'lymphocytes_normal_range',
        'monocytes_result',
        'monocytes_normal_range',
        'eosinophils_result',
        'eosinophils_normal_range',
        'basophils_result',
        'basophils_normal_range',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
