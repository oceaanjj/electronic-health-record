<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IvsAndLine extends Model
{
    use HasFactory;
    protected $table = 'ivs_and_lines';
    protected $fillable = [
        'patient_id',
        'iv_fluid',
        'rate',
        'site',
        'status',
    ];
    

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
