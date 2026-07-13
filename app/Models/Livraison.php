<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    protected $fillable = [
        'programme_id',
        'numero',
        'reference',
        'date',
        'livreur',
        'receptionniste',
        'description',
        'quantite',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneLivraison::class);
    }
}