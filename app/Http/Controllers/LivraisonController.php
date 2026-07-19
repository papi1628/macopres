<?php

namespace App\Http\Controllers;

use App\Models\LigneBonCommande;
use App\Models\LigneLivraison;
use App\Models\Livraison;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LivraisonController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES BORDEREAUX D'UN PROGRAMME
    |--------------------------------------------------------------------------
    */
    public function index(Programme $programme)
    {
        $programme->load('ecole', 'livraisons.lignes.ligneBonCommande');

        return view('livraisons.index', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE — le livreur coche ce qui est livré, basé sur les BC
    |--------------------------------------------------------------------------
    */
    public function create(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.lignes.designation');

        // Pour chaque ligne de chaque BC, calcule ce qu'il reste à livrer
        $lignes = $programme->bonsCommande->flatMap->lignes
            ->map(function ($ligne) {
                $dejaLivre    = LigneLivraison::where('ligne_bon_commande_id', $ligne->id)->sum('quantite_livree');
                $ligne->reste = $ligne->quantite - $dejaLivre;
                return $ligne;
            })
            ->filter(fn($ligne) => $ligne->reste > 0)
            ->values();

        return view('livraisons.create', compact('programme', 'lignes'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER UNE LIVRAISON
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Programme $programme)
    {
        $request->validate([
            'livreur'      => 'nullable|string|max:255',
            'date'         => 'nullable|date',
            'quantites'    => 'required|array',
            'quantites.*'  => 'nullable|integer|min:0',
        ]);

        $lignesLivrees = collect($request->quantites)->filter(fn($qte) => $qte > 0);

        if ($lignesLivrees->isEmpty()) {
            return back()->withInput()->with('error', 'Indiquez au moins une quantité livrée.');
        }

        // Vérifie qu'on ne livre jamais plus que ce qu'il reste à livrer
        foreach ($lignesLivrees as $ligneId => $qte) {
            $ligne = LigneBonCommande::findOrFail($ligneId);
            $dejaLivre = LigneLivraison::where('ligne_bon_commande_id', $ligneId)->sum('quantite_livree');
            $reste = $ligne->quantite - $dejaLivre;

            if ($qte > $reste) {
                return back()->withInput()->with('error',
                    "La quantité livrée pour \"{$ligne->libelle()}\" ({$ligne->taille}) dépasse ce qu'il reste à livrer ({$reste})."
                );
            }
        }

        $rang   = Livraison::whereHas('programme', fn($q) => $q->where('ecole_id', $programme->ecole_id))->count() + 1;
        $numero = str_pad($rang, 3, '0', STR_PAD_LEFT) . $programme->ecole->initiales();

        $livraison = $programme->livraisons()->create([
            'numero'    => $numero,
            'reference' => 'Macopres Confection',
            'date'      => $request->date ?: now()->format('Y-m-d'),
            'livreur'   => $request->livreur,
            'quantite'  => $lignesLivrees->sum(),
        ]);

        foreach ($lignesLivrees as $ligneId => $qte) {
            $livraison->lignes()->create([
                'ligne_bon_commande_id' => $ligneId,
                'quantite_livree'       => $qte,
            ]);
        }

        $programme->load('bonsCommande.lignes');


        $totalCommande = $programme->bonsCommande
            ->flatMap->lignes
            ->sum('quantite');


        $totalLivre = LigneLivraison::whereHas('ligneBonCommande.bonCommande', function($q) use($programme){
                $q->where('programme_id',$programme->id);
            })
            ->sum('quantite_livree');


        if($totalLivre >= $totalCommande){
            $programme->update([
                'statut'=>'terminee'
            ]);
        }

        // Vérifie si toutes les quantités du programme sont livrées
        $programme->verifierTerminaison();

        return redirect()->route('programmes.livraisons.imprimer', $livraison)->with('success', 'Livraison enregistrée.');
    }

    public function update(Request $request, Livraison $livraison)
    {
        $request->validate([
            'date'=>'required|date',
            'livreur'=>'nullable|string',
            'quantites'=>'required|array'
        ]);


        DB::transaction(function() use($request,$livraison){

            $livraison->update([
                'date'=>$request->date,
                'livreur'=>$request->livreur,
            ]);


            foreach($request->quantites as $ligneId=>$quantite){

                LigneLivraison::where('id',$ligneId)
                    ->where('livraison_id',$livraison->id)
                    ->update([
                        'quantite_livree'=>$quantite
                    ]);
            }


            $livraison->update([
                'quantite'=>$livraison->lignes()
                    ->sum('quantite_livree')
            ]);

            $livraison->programme->verifierTerminaison();

        });


        return response()->json([
            'success'=>true
        ]);
    }

    public function destroy(Livraison $livraison)
    {
        $programme = $livraison->programme;

        $livraison->delete();

        // Recalcule le statut après suppression
        $programme->verifierTerminaison();

        return response()->json([
            'success' => true,
            'message' => 'Livraison supprimée.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | BORDEREAU IMPRIMABLE
    |--------------------------------------------------------------------------
    */
    public function imprimer(Livraison $livraison)
    {
        $livraison->load('programme.ecole', 'lignes.ligneBonCommande.designation');

        // Progression de la commande concernée par CE bordereau (lignes touchées uniquement)
        $ligneIds         = $livraison->lignes->pluck('ligne_bon_commande_id');
        $totalCommande    = LigneBonCommande::whereIn('id', $ligneIds)->sum('quantite');
        $totalLivreCumule = LigneLivraison::whereIn('ligne_bon_commande_id', $ligneIds)->sum('quantite_livree');

        return view('livraisons.imprimer', compact('livraison', 'totalCommande', 'totalLivreCumule'));
    }

    /*
    |--------------------------------------------------------------------------
    | TABLEAU DE SUIVI DES LIVRAISONS (agrégé sur tout le programme)
    |--------------------------------------------------------------------------
    */
    public function suivi(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.lignes.designation');

        $groupes = $programme->bonsCommande->flatMap->lignes
            ->groupBy(function ($ligne) {
                return implode('|', [
                    $ligne->designation_id ?? ('libre:' . $ligne->designation_libre),
                    $ligne->couleur ?? '',
                    $ligne->matiere ?? '',
                    $ligne->logo ? '1' : '0',
                ]);
            })
            ->map(function ($lignes) {
                $premiere = $lignes->first();

                $tailles = $lignes->groupBy('taille')->map(function ($groupeTaille) {
                    $commande = $groupeTaille->sum('quantite');
                    $livre = LigneLivraison::whereIn('ligne_bon_commande_id', $groupeTaille->pluck('id'))->sum('quantite_livree');
                    return ['commande' => $commande, 'livre' => $livre];
                });

                return [
                    'libelle'        => $premiere->libelle(),
                    'tailles'        => $tailles,
                    'total_commande' => $lignes->sum('quantite'),
                    'total_livre'    => $tailles->sum('livre'),
                ];
            })
            ->values();

        return view('livraisons.suivi', compact('programme', 'groupes'));
    }
}