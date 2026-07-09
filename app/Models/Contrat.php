<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'programme_id',
        'bon_commande_id',
        'statut',
        'description_engagement',
        'montant_total',
        'date_limite_livraison',
        'delai_livraison_texte',
        'representant_macopres',
        'representant_client',
        'representant_client_role',
        'date_signature',
    ];

    protected $casts = [
        'date_limite_livraison' => 'date',
        'date_signature'        => 'date',
        'montant_total'         => 'decimal:2',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    /**
     * Le premier bon de commande du programme — celui dont le contrat tire
     * son montant et son engagement. Le contrat n'est jamais lié aux BC suivants.
     */
    public function bonCommande()
    {
        return $this->belongsTo(BonCommande::class);
    }

    public function estSigne(): bool
    {
        return $this->statut === 'signe';
    }

    public function libelleStatut(): string
    {
        return $this->estSigne() ? 'Signé' : 'Brouillon';
    }
}