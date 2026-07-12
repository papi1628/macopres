<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FicheProductionGroupe extends Model
{
    protected $table = 'fiche_production_groupes';

    protected $fillable = [
        'bon_commande_id',
        'groupe_cle',
        'description',
        'photo',
    ];

    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}