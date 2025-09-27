<?php
namespace App\Models\MedicalHistory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;

class DevelopmentalHistory extends Model
{
    protected $table = 'developmental_history';
    protected $primaryKey = 'patient_id';
    protected $fillable = [
        'patient_id',
        'gross_motor',
        'fine_motor',
        'language',
        'cognitive',
        'social'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}