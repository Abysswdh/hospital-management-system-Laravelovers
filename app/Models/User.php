<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Patient;
use App\Models\Doctor;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // RELASI PATIENT
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    // RELASI DOCTOR
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }
}