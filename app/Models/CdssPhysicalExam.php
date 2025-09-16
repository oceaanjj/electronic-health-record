<?php


//TODO:
/* 
[] fillables
[] relationship to patient_id
[] connect to physical exam

*/

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdssPhysicalExam extends Model {

    use HasFactory;
    protected $table = 'cdss_physical_exam';
    
}
