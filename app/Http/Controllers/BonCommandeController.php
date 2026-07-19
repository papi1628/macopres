<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Programme;
use App\Models\BonCommande;
use App\Models\LigneBonCommande;
use App\Models\Designation;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\FactureController;
use Illuminate\Support\Facades\Log;

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

    public function imprimer(BonCommande $bonCommande)
    {
        $bonCommande->load('programme.ecole', 'lignes.designation');

        return view('bons.imprimer', compact('bonCommande'));
    }

    public function updateCondition(Request $request, BonCommande $bonCommande)
    {
        $request->validate(['condition_paiement' => 'nullable|string|max:255']);
        $bonCommande->update(['condition_paiement' => $request->condition_paiement]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'            => true,
                'condition_paiement' => $bonCommande->condition_paiement,
            ]);
        }

        return back()->with('success', 'Condition de paiement mise à jour.');
    }

    public function destroy(BonCommande $bonCommande)
    {
        $programme = $bonCommande->programme;
        $bonCommande->delete();
        return back()->with('success', 'Bon de commande supprimé.');
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

        $ligne = $bonCommande->lignes()->create([
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

        // Le contrat (BC1 uniquement) et la facture (chaque BC) se génèrent/actualisent automatiquement.
        // Protégé : si la synchro échoue, l'article reste enregistré et la réponse JSON part quand même —
        // sinon une erreur ici casse le fetch() côté front alors que la sauvegarde a réussi.
        $this->synchroniserSansCasser($bonCommande);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'ligne'   => $this->ligneAsArray($ligne),
                'montant' => $bonCommande->montant,
            ]);
        }

        return back()->with('success', 'Article ajouté au bon de commande.');
    }

    public function updateLigneBonCommande(Request $request, LigneBonCommande $ligneBonCommande)
    {
        $request->validate([
            'designation_id'    => 'nullable|exists:designations,id',
            'designation_libre' => 'nullable|string|max:255',
            'taille'            => 'nullable|string|max:20',
            'couleur'           => 'nullable|string|max:100',
            'matiere'           => 'nullable|string|max:100',
            'logo'              => 'nullable|boolean',
            'quantite'          => 'required|integer|min:1',
            'prix_unitaire'     => 'required|numeric|min:0',
        ]);

        $ligneBonCommande->update([
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

        $bonCommande = $ligneBonCommande->bonCommande;
        $bonCommande->recalculerMontant();
        $this->synchroniserSansCasser($bonCommande);

        return response()->json([
            'success' => true,
            'ligne'   => $this->ligneAsArray($ligneBonCommande->fresh()),
            'montant' => $bonCommande->montant,
        ]);
    }

    public function destroyLigneBonCommande(Request $request, LigneBonCommande $ligneBonCommande)
    {
        $bonCommande = $ligneBonCommande->bonCommande;
        $ligneBonCommande->delete();
        $bonCommande->recalculerMontant();

        $this->synchroniserSansCasser($bonCommande);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'montant' => $bonCommande->montant]);
        }

        return back()->with('success', 'Article supprimé du bon de commande.');
    }

    /**
     * Appelle la synchronisation du contrat et de la facture sans jamais faire planter
     * la réponse HTTP en cours : une erreur ici est journalisée mais n'empêche pas
     * l'utilisateur de recevoir la confirmation que son article a bien été enregistré.
     */
    private function synchroniserSansCasser(BonCommande $bonCommande): void
    {
        try {
            ContratController::synchroniser($bonCommande);
        } catch (\Throwable $e) {
            Log::error('Échec synchronisation contrat depuis BC ' . $bonCommande->id, ['message' => $e->getMessage()]);
        }

        try {
            FactureController::synchroniser($bonCommande);
        } catch (\Throwable $e) {
            Log::error('Échec synchronisation facture depuis BC ' . $bonCommande->id, ['message' => $e->getMessage()]);
        }
    }

    /**
     * Représentation JSON homogène d'une ligne, utilisée par les réponses AJAX
     * (ajout, modification) pour que le front puisse reconstruire la ligne du tableau.
     */
    private function ligneAsArray(LigneBonCommande $ligne): array
    {
        return [
            'id'               => $ligne->id,
            'designation'      => $ligne->libelle(),
            'designation_libre'=> $ligne->designation_libre,
            'taille'           => $ligne->taille,
            'couleur'          => $ligne->couleur,
            'matiere'          => $ligne->matiere,
            'quantite'         => $ligne->quantite,
            'prix_unitaire'    => $ligne->prix_unitaire,
            'montant_ligne'    => $ligne->montant_ligne,
            'logo'             => (bool) $ligne->logo,
            'bon_commande_id'  => $ligne->bon_commande_id,
        ];
    }
}