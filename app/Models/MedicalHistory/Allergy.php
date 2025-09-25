<?php
namespace App\Models\MedicalHistory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Patient;
class Allergy extends Model
{
    protected $table = 'allergies';
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