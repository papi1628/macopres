<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneLivraison extends Model
{
    protected $table = 'lignes_livraison';

    protected $fillable = [
        'livraison_id',
        'ligne_bon_commande_id',
        'quantite_livree',
    ];

    public function livraison()
    {
        return $this->belongsTo(Livraison::class);
    }

    public function ligneBonCommande()
    {
        return $this->belongsTo(LigneBonCommande::class);
    }
}