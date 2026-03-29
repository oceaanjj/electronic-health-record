<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    //  Fields that can be mass assigned
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'full_name',
        'birthdate',
        'age',
        'sex',
        'address',
        'birthplace',
    ];

    // Hide sensitive fields (important for API / JSON responses)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //  Casts
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //  Relationships
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'user_id', 'id');
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = strtolower((string) $value);
    }
}
