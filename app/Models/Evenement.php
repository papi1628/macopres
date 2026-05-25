<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'titre',
        'description',
        'est_paye',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'est_paye' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | HELPERS 
    |--------------------------------------------------------------------------
    */

    public function isFerie(): bool
    {
        return $this->type === 'ferie';
    }

    /*public function isRepos(): bool
    {
        return $this->type === 'repos';
    }

    public function isEvenement(): bool
    {
        return $this->type === 'evenement';
    }*/

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR UTILE POUR UI
    |--------------------------------------------------------------------------
    */

    public function getBadgeAttribute(): array
    {
        return match ($this->type) {
            'ferie' => [
                'label' => 'Férié',
                'bg' => '#DBEAFE',
                'color' => '#1D4ED8',
            ],
            /*'repos' => [
                'label' => 'Repos',
                'bg' => '#F3F4F6',
                'color' => '#374151',
            ],
            'evenement' => [
                'label' => 'Événement',
                'bg' => '#DCFCE7',
                'color' => '#166534',
            ],*/
            default => [
                'label' => 'Autre',
                'bg' => '#E5E7EB',
                'color' => '#111827',
            ],
        };
    }
}