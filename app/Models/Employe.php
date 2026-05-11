<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    protected $fillable = [
        'matricule',
        'nom',
        'user_id',
        'prenom',
        'tel',
        'poste',
        'departement',
        'qr_code',
        'actif',
        'date_embauche',
        'salaire',
        'created_by',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'date_embauche' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function pointages()
    {
        return $this->hasMany(Pointage::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}