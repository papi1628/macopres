<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'login',
        'password',
        'role',
        'employe_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function pointages()
    {
        return $this->hasMany(Pointage::class, 'cree_par');
    }

    public function employesCrees()
    {
        return $this->hasMany(Employe::class, 'created_by');
    }

    public function employe()
    {
        return $this->hasOne(Employe::class);
    }

    public function employeUser()
    {
        return $this->belongsTo(Employe::class);
    }

}