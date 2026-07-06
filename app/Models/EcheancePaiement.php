<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EcheancePaiement extends Model
{
    protected $table = 'echeances_paiement';

    protected $fillable = [
        'programme_id',
        'numero_versement',
        'date_prevue',
        'montant_prevu',
    ];

    protected $casts = [
        'date_prevue'   => 'date',
        'montant_prevu' => 'decimal:2',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }
}