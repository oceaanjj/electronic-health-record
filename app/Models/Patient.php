<?php

/* Models is basically what interacts with the DB
 Dito nakalagay yung mga code na may Query 

 May different ways para makipag-interact sa DB such as 
 Raw queries, Query builder, or the Eloquent ORM

 Make sure to note na gamit natin is Eloquent ORM para 
 same-same tayo at hindi nakakaconfuse basahin yung code. 

*/

namespace App\Models;

use App\Http\Controllers\PatientController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'patient_id';

    // optional lang to.
    protected $table = 'patients';

    // basically declaration na itong mga columns nato is puwedeng lagyan ng value
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'age',
        'sex',
        'address',
        'birthplace',
        'religion',
        'ethnicity',
        'chief_complaints',
        'admission_date',
        'room_no',
        'bed_no',
        'contact_name',
        'contact_relationship',
        'contact_number',

        'user_id',
    ];

    // rs to records, one patient -> many dpie
    // add another one for each modules that need a dpie or need a relation the patient_id
    public function physicalExams()
    {
        return $this->hasMany(\App\Models\PhysicalExam::class, 'patient_id');
    }

    public function intakeAndOutputs()
    {
        return $this->hasMany(\App\Models\IntakeAndOutput::class, 'patient_id');
    }

    public function diagnosticImages()
    {
        return $this->hasMany(\App\Models\DiagnosticImage::class, 'patient_id');
    }
    protected $casts = [
        'admission_date' => 'datetime',
    ];

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst(strtolower($value));
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst(strtolower($value));
    }

    public function setMiddleNameAttribute($value)
    {
        $this->attributes['middle_name'] = $value ? ucfirst(strtolower($value)) : null;
    }

    public function getNameAttribute()
    {
        // capitalize first and last names for display
        $firstName = ucfirst(strtolower($this->first_name));
        $lastName = ucfirst(strtolower($this->last_name));

        // determine middle initial, capitalizing if present
        $middleInitial = '';
        if ($this->middle_name) {
            $middleInitial = strtoupper(substr($this->middle_name, 0, 1)) . '.';
        }

        // return formatted full name
        return trim("{$lastName}, {$firstName} {$middleInitial}");
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Determine the age group of the patient.
     * This is a placeholder and can be expanded for more precise age grouping.
     *
     * @return string
     */
    public function getAgeGroup(): string
    {
        $age = $this->age; // Assuming 'age' attribute is available and an integer

        if ($age === null) {
            return 'unknown';
        }

        if ($age <= 0.083) { // Roughly 0-1 month
            return 'neonate';
        } elseif ($age >= 0.084 && $age <= 2) { // 1 month to 2 years
            return 'infant';
        } elseif ($age > 2 && $age <= 12) { // 2 years to 12 years
            return 'child';
        } elseif ($age > 12 && $age <= 18) { // 12 years to 18 years
            return 'adolescent';
        } else {
            return 'adult'; // For ages above 18
        }
    }
}
