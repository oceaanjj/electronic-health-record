<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnostic extends Model
{
    protected $table = 'diagnostics';

    protected $fillable = [
        'patient_id',
        'type',
        'path',
        'original_name',
    ];

        public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}