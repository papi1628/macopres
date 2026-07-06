<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    protected $fillable = [
        'programme_id',
        'date',
        'montant',
        'mode_paiement',
        'reference',
        'recu_par',
    ];

    protected $casts = [
        'date'    => 'date',
        'montant' => 'decimal:2',
    ];

    public function programme()
    {
        return $this->belongsTo(Programme::class);
    }

    public function receveur()
    {
        return $this->belongsTo(User::class, 'recu_par');
    }

    public function modeLabel(): string
    {
        return match ($this->mode_paiement) {
            'cheque'        => 'Chèque',
            'virement'      => 'Virement bancaire',
            'wave'          => 'Wave',
            'orange_money'  => 'Orange Money',
            'espece'        => 'Espèces',
            'agent_mandate' => 'Agent mandaté',
            default         => $this->mode_paiement,
        };
    }
}