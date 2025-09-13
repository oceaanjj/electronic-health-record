<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    protected $table = 'users';
     protected $fillable = [
        'name',
        'password',
    ];
}