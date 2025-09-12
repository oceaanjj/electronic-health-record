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

class Patient extends Model
{
    use HasFactory;

    // optional lang to.
    protected $table = 'patients';

    // basically declaration na itong mga columns nato is puwedeng lagyan ng value
    protected $fillable = [
        'name',
        'age',
        'sex',
        'address',
        'birthplace',
        'religion',
        'ethnicity',
        'chief_complaints',
        'admission_date',
    ];
}
