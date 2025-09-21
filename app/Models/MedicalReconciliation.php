<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalReconciliation extends Model
{
    use HasFactory;
    protected $table = 'current_medication';
    protected $fillable = [
        'patient_id',
        'current_med',
        'current_dose',
        'current_route',
        'current_frequency',
        'current_indication',
        'current_text',
    ];
    

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

class HomeMedication extends Model
{
    use HasFactory;
    protected $table = 'home_medication';
    protected $fillable = [
        'patient_id',
        'home_med',
        'home_dose',
        'home_route',
        'home_frequency',
        'home_indication',
        'home_text',
    ];
    

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

class ChangesInMedication extends Model
{
    use HasFactory;
    protected $table = 'changes_in_medication';
    protected $fillable = [
        'patient_id',
        'change_med',
        'change_dose',
        'change_route',
        'change_frequency',
        'change_indication',
        'change_text',
    ];
    

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
