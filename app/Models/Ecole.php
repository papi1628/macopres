<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ecole extends Model
{
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'contact_nom',
        'contact_telephone',
        'created_by',
    ];

    public function programmes()
    {
        return $this->hasMany(Programme::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Initiales de l'école pour les numéros de documents (factures, bordereaux...).
     * Ex : "Collège Saint Charles Lwanga" -> "CSCL". Ignore les petits mots de liaison.
     */
    public function initiales(): string
    {
        $motsVides = ['de', 'du', 'des', 'la', 'le', 'les', 'et', 'l', 'd', 'au', 'aux'];

        $initiales = collect(preg_split('/[\s\'\-]+/', $this->nom))
            ->filter()
            ->reject(fn($mot) => in_array(mb_strtolower($mot), $motsVides))
            ->map(fn($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
            ->implode('');

        return mb_substr($initiales, 0, 4) ?: 'XXX';
    }
}