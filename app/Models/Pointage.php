<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pointage extends Model
{
    protected $fillable = [
        'employe_id',
        'cree_par',
        'date',
        'arrivee',
        'sortie',
        'statut',
        'methode',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }
}