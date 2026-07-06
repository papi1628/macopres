<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    protected $fillable = [
        'programme_id',
        'description_engagement',
        'montant_total',
        'date_limite_livraison',
        'delai_livraison_texte',
        'representant_macopres',
        'representant_client',
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
}