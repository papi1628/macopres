<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneBonCommande extends Model
{
    protected $table = 'lignes_bon_commande';

    protected $fillable = [
        'bon_commande_id',
        'designation_id',
        'designation_libre',
        'taille',
        'couleur',
        'matiere',
        'logo',
        'quantite',
        'prix_unitaire',
        'montant_ligne',
    ];

    protected $casts = [
        'logo'          => 'boolean',
        'prix_unitaire' => 'decimal:2',
        'montant_ligne' => 'decimal:2',
    ];

    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function libelle(): string
    {
        return $this->designation?->nom ?? $this->designation_libre ?? 'Article';
    }
}