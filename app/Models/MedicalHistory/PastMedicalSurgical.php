<?php
namespace App\Models\MedicalHistory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
class PastMedicalSurgical extends Model
{
    protected $table = 'past_medical_surgical';
    protected $primaryKey = 'patient_id';
    protected $fillable = [
        'patient_id',
        'condition_name',
        'description',
        'medication',
        'dosage',
        'side_effect',
        'comment'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}