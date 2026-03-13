<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormRead extends Model
{
    protected $table = 'form_reads';

    protected $fillable = ['user_id', 'model_type', 'model_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
