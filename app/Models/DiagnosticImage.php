<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticImage extends Model
{
    protected $fillable = [
        'patient_id',
        'image_type',
        'image_path',
    ];

    // Relationship sa Patient
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }
}
