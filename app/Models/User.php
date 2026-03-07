<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ✅ Fields that can be mass assigned
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    // ✅ Hide sensitive fields (important for API / JSON responses)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Casts
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🔹 Relationships
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'user_id', 'id');
    }
}
