<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = [
        'nom',
        'manche',
        'prix_defaut',
        'created_by',
    ];

    protected $casts = [
        'prix_defaut' => 'decimal:2',
    ];

    public function lignesBonCommande()
    {
        return $this->hasMany(LigneBonCommande::class);
    }

    public function libelle(): string
    {
        return match ($this->manche) {
            'courte' => $this->nom . ' (manche courte)',
            'longue' => $this->nom . ' (manche longue)',
            default  => $this->nom,
        };
    }
}