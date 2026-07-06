<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Programme extends Model
{
    protected $fillable = [
        'ecole_id',
        'annee_scolaire',
        'statut',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function ecole()
    {
        return $this->belongsTo(Ecole::class);
    }

    public function contrat()
    {
        return $this->hasOne(Contrat::class);
    }

    public function bonsCommande()
    {
        return $this->hasMany(BonCommande::class);
    }

    public function echeancesPaiement()
    {
        return $this->hasMany(EcheancePaiement::class)->orderBy('numero_versement');
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class)->orderBy('date', 'desc');
    }

    public function articlesProduction()
    {
        return $this->hasMany(ArticleProduction::class);
    }

    public function livraisons()
    {
        return $this->hasMany(Livraison::class)->orderBy('date', 'desc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Logique métier
    |--------------------------------------------------------------------------
    */

    /**
     * Montant total dû = somme des bons de commande (fallback : montant du contrat).
     */
    public function montantTotal(): float
    {
        $total = $this->bonsCommande->sum('montant');
        return $total > 0 ? $total : (float) ($this->contrat->montant_total ?? 0);
    }

    public function montantPaye(): float
    {
        return (float) $this->paiements->sum('montant');
    }

    public function solde(): float
    {
        return $this->montantTotal() - $this->montantPaye();
    }

    public function tauxPaiement(): int
    {
        $total = $this->montantTotal();
        return $total > 0 ? (int) round(($this->montantPaye() / $total) * 100) : 0;
    }
}