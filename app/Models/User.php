<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;



class User extends Authenticatable
{
    protected $fillable = ['username', 'email', 'password', 'role'];

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'id');
    }
}

