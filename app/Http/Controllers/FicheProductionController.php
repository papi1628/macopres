<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\FicheProductionGroupe;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FicheProductionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES FICHES DE PRODUCTION D'UN PROGRAMME (une par BC)
    |--------------------------------------------------------------------------
    */
    public function index(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.lignes');

        return view('fiches.index', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | FICHE DE GESTION (ajout description / photo par groupe d'articles)
    |--------------------------------------------------------------------------
    */
    public function show(BonCommande $bonCommande)
    {
        $bonCommande->load('programme.ecole', 'lignes.designation');
        $groupes = $this->grouper($bonCommande);

        return view('fiches.show', compact('bonCommande', 'groupes'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER LA DESCRIPTION / PHOTO D'UN GROUPE D'ARTICLES
    |--------------------------------------------------------------------------
    */
    public function storeNote(Request $request, BonCommande $bonCommande)
    {
        $request->validate([
            'groupe_cle'  => 'required|string',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|max:4096',
        ]);

        $groupe = FicheProductionGroupe::firstOrNew([
            'bon_commande_id' => $bonCommande->id,
            'groupe_cle'      => $request->groupe_cle,
        ]);

        $groupe->description = $request->description;

        if ($request->hasFile('photo')) {
            if ($groupe->photo) {
                Storage::disk('public')->delete($groupe->photo);
            }
            $groupe->photo = $request->file('photo')->store('production', 'public');
        }

        $groupe->save();

        return back()->with('success', 'Précisions enregistrées.');
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT IMPRIMABLE
    |--------------------------------------------------------------------------
    */
    public function imprimer(BonCommande $bonCommande)
    {
        $bonCommande->load('programme.ecole', 'lignes.designation');
        $groupes = $this->grouper($bonCommande);

        $tousLesBons = $bonCommande->programme->bonsCommande()->orderBy('id')->pluck('id');
        $rang        = $tousLesBons->search($bonCommande->id) + 1;

        $titre  = $rang === 1 ? 'FICHE DE PRODUCTION' : 'FICHE DE PRODUCTION RAJOUT';
        $numero = 'N°' . str_pad($rang, 2, '0', STR_PAD_LEFT);

        return view('fiches.imprimer', compact('bonCommande', 'groupes', 'titre', 'numero'));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — regroupe les lignes du BC par désignation + couleur + matière + logo
    |--------------------------------------------------------------------------
    */
    private function grouper(BonCommande $bonCommande)
    {
        $notes = FicheProductionGroupe::where('bon_commande_id', $bonCommande->id)
            ->get()
            ->keyBy('groupe_cle');

        return $bonCommande->lignes
            ->groupBy(function ($ligne) {
                return implode('|', [
                    $ligne->designation_id ?? ('libre:' . $ligne->designation_libre),
                    $ligne->couleur ?? '',
                    $ligne->matiere ?? '',
                    $ligne->logo ? '1' : '0',
                ]);
            })
            ->map(function ($lignes, $cleBrute) use ($notes) {
                $cle      = md5($cleBrute);
                $premiere = $lignes->first();

                return [
                    'cle'     => $cle,
                    'libelle' => $premiere->libelle(),
                    'couleur' => $premiere->couleur,
                    'matiere' => $premiere->matiere,
                    'logo'    => $premiere->logo,
                    'tailles' => $lignes->groupBy('taille')->map(fn($l) => $l->sum('quantite')),
                    'total'   => $lignes->sum('quantite'),
                    'note'    => $notes->get($cle),
                ];
            })
            ->values();
    }
}