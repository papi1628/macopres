<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\Facture;
use App\Models\Programme;

class FactureController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES FACTURES D'UN PROGRAMME
    |--------------------------------------------------------------------------
    */
    public function index(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.facture', 'bonsCommande.lignes');

        return view('factures.index', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION AUTOMATIQUE
    |--------------------------------------------------------------------------
    | À appeler depuis BonCommandeController après tout ajout/suppression/
    | modification d'article. Contrairement au contrat, CHAQUE bon de commande
    | a sa propre facture (le contrat, lui, ne concerne que le premier BC).
    | Le numéro n'est attribué qu'une seule fois, à la création.
    */
    public static function synchroniser(BonCommande $bonCommande): void
    {
        $facture = $bonCommande->facture;

        if (!$facture) {
            $bonCommande->loadMissing('programme.ecole');

            $rang      = Facture::count() + 1;
            $initiales = self::initiales($bonCommande->programme->ecole->nom);
            $numero    = str_pad($rang, 3, '0', STR_PAD_LEFT) . '-' . now()->year . '-' . $initiales;

            Facture::create([
                'bon_commande_id' => $bonCommande->id,
                'numero'          => $numero,
                'date'            => now()->format('Y-m-d'),
                'montant'         => $bonCommande->montant,
            ]);

            return;
        }

        $facture->update(['montant' => $bonCommande->montant]);
    }

    /**
     * Initiales de l'école pour le numéro de facture (ex : "Collège Saint Charles Lwanga" -> "CSCL").
     * Ignore les petits mots de liaison (de, la, les, et...).
     */
    private static function initiales(string $nom): string
    {
        $motsVides = ['de', 'du', 'des', 'la', 'le', 'les', 'et', 'l', 'd', 'au', 'aux'];

        $initiales = collect(preg_split('/[\s\'\-]+/', $nom))
            ->filter()
            ->reject(fn($mot) => in_array(mb_strtolower($mot), $motsVides))
            ->map(fn($mot) => mb_strtoupper(mb_substr($mot, 0, 1)))
            ->implode('');

        return mb_substr($initiales, 0, 4) ?: 'XXX';
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT IMPRIMABLE
    |--------------------------------------------------------------------------
    */
    public function imprimer(BonCommande $bonCommande)
    {
        $bonCommande->load('programme.ecole', 'lignes.designation', 'facture');

        if (!$bonCommande->facture) {
            return redirect()->route('programmes.bons.show', $bonCommande)
                ->with('error', "La facture n'a pas encore été générée (ajoutez au moins un article).");
        }

        return view('factures.imprimer', compact('bonCommande'));
    }
}