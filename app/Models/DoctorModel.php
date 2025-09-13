<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorModel extends Model
{
    protected $table = 'users';
     protected $fillable = [
        'name',
        'password',
    ];
}