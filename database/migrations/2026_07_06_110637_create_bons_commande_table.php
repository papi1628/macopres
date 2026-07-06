<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonCommande extends Model
{
    protected $table = 'bons_commande';

    protected $fillable = [
        'programme_id',
        'numero',
        'date',
        'montant',
        'nature',
        'condition_paiement',
    ];

    protected $casts = [
        'date'    => 'date',
        'montant' => 'decimal:2',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function lignes()
    {
        return $this->hasMany(LigneBonCommande::class);
    }

    /**
     * Recalcule et sauvegarde le montant total du bon à partir de ses lignes.
     * À appeler après tout ajout/suppression/modification de ligne.
     */
    public function recalculerMontant(): void
    {
        $this->montant = $this->lignes()->sum('montant_ligne');
        $this->save();
    }

    /**
     * Quantité totale d'articles commandés (toutes lignes confondues).
     */
    public function quantiteTotale(): int
    {
        return (int) $this->lignes()->sum('quantite');
    }

    /**
     * Liste des conditions de paiement proposées par le système.
     */
    public static function conditionsProposees(): array
    {
        return [
            'VOIR CONTRAT',
            'A LA LIVRAISON',
            '50% ACOMPTE, 50% A LA LIVRAISON',
        ];
    }
}