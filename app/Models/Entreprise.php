<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entreprise extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'devise',
        'logo',
    ];

    /**
     * Récupère la ligne de configuration unique, en la créant si elle n'existe pas.
     */
    public static function courante(): self
    {
        return static::firstOrCreate([], [
            'nom'    => 'MACOPRES',
            'devise' => 'FCFA',
        ]);
    }

    public function logoUrl(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }
}