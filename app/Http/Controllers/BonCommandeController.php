<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\Designation;

class BonCommandeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE / TIMELINE DES BONS DE COMMANDE D'UN PROGRAMME
    |--------------------------------------------------------------------------
    */
    public function index(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.lignes.designation');
        $designations = Designation::orderBy('nom')->get();

        return view('bons.index', compact('programme', 'designations'));
    }

    /*
    |--------------------------------------------------------------------------
    | CRÉER UNE NOUVELLE COMMANDE (le système génère tout, aucun formulaire)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Programme $programme)
    {
        $rang   = $programme->bonsCommande()->count() + 1;
        $numero = 'BC-' . now()->year . '-' . str_pad($rang, 4, '0', STR_PAD_LEFT);

        $bon = $programme->bonsCommande()->create([
            'numero'             => $numero,
            'date'               => now()->format('Y-m-d'),
            'nature'             => 'Uniformes scolaires ' . $programme->annee_scolaire,
            'condition_paiement' => null,
            'montant'            => 0,
        ]);

        return back()->with('bon_ouvert', $bon->id);
    }

    public function show(BonCommande $bonCommande)
    {
        $bonCommande->load('programme.ecole', 'lignes.designation');
        $designations = Designation::orderBy('nom')->get();

        return view('bons.show', compact('bonCommande', 'designations'));
    }

    public function update(Request $request, BonCommande $bonCommande)
    {
        $request->validate(['condition_paiement' => 'nullable|string|max:255']);
        $bonCommande->update(['condition_paiement' => $request->condition_paiement]);
        return back()->with('success', 'Condition de paiement mise à jour.');
    }

    public function destroy(BonCommande $bonCommande)
    {
        $programme = $bonCommande->programme;
        $bonCommande->delete();
        return redirect()->route('programmes.show', $programme)->with('success', 'Bon de commande supprimé.');
    }

    /*
    |--------------------------------------------------------------------------
    | LIGNES DE BON DE COMMANDE — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storeLigneBonCommande(Request $request, BonCommande $bonCommande)
    {
        $request->validate([
            'designation_id'    => 'nullable|exists:designations,id',
            'designation_libre' => 'required_without:designation_id|nullable|string|max:255',
            'taille'            => 'nullable|string|max:20',
            'couleur'           => 'nullable|string|max:100',
            'matiere'           => 'nullable|string|max:100',
            'logo'              => 'nullable|boolean',
            'quantite'          => 'required|integer|min:1',
            'prix_unitaire'     => 'required|numeric|min:0',
        ]);

        $bonCommande->lignes()->create([
            'designation_id'    => $request->designation_id,
            'designation_libre' => $request->designation_libre,
            'taille'            => $request->taille,
            'couleur'           => $request->couleur,
            'matiere'           => $request->matiere,
            'logo'              => $request->boolean('logo'),
            'quantite'          => $request->quantite,
            'prix_unitaire'     => $request->prix_unitaire,
            'montant_ligne'     => $request->quantite * $request->prix_unitaire,
        ]);

        $bonCommande->recalculerMontant();

        // Le contrat se génère/actualise automatiquement — uniquement si ce BC est le premier du programme.
        ContratController::synchroniser($bonCommande);

        return back()->with('success', 'Article ajouté au bon de commande.');
    }

    public function destroyLigneBonCommande(LigneBonCommande $ligneBonCommande)
    {
        $bonCommande = $ligneBonCommande->bonCommande;
        $ligneBonCommande->delete();
        $bonCommande->recalculerMontant();

        ContratController::synchroniser($bonCommande);

        return back()->with('success', 'Article supprimé du bon de commande.');
    }
}