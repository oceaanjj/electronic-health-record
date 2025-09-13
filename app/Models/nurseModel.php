<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NurseModel extends Model
{
    protected $table = 'nurse_logins';
     protected $fillable = [
        'nurse_id',
        'name',
        'password',
    ];
}