<?php

namespace App\Http\Controllers;

use App\Models\BonCommande;
use App\Models\Contrat;
use App\Models\EcheancePaiement;
use App\Models\Programme;
use Illuminate\Http\Request;

class ContratController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SYNCHRONISATION AUTOMATIQUE
    |--------------------------------------------------------------------------
    | À appeler depuis BonCommandeController après tout ajout/suppression
    | d'article. Le contrat n'est lié qu'au PREMIER bon de commande du
    | programme : ses articles suivants (BC 2, BC 3...) ne le modifient pas.
    | L'utilisateur ne remplit rien — le contrat se déduit de l'école et
    | des articles du premier BC.
    */
    public static function synchroniser(BonCommande $bonCommande): void
    {
        $programme  = $bonCommande->programme;
        $premierBon = $programme->bonsCommande()->orderBy('id')->first();

        // On ne synchronise que si c'est bien le premier BC du programme
        if (!$premierBon || $premierBon->id !== $bonCommande->id) {
            return;
        }

        $bonCommande->load('lignes.designation');
        $programme->loadMissing('ecole');

        $contrat = $programme->contrat ?: new Contrat([
            'programme_id'          => $programme->id,
            'statut'                => 'brouillon',
            'representant_macopres' => 'Masse BA',
        ]);

        $contrat->bon_commande_id        = $bonCommande->id;
        $contrat->montant_total          = $bonCommande->montant;
        $contrat->description_engagement = self::composerEngagement($bonCommande);

        // Représentant client par défaut = contact renseigné sur la fiche école (modifiable ensuite)
        if (!$contrat->representant_client && $programme->ecole->contact_nom) {
            $contrat->representant_client = $programme->ecole->contact_nom;
        }

        $contrat->save();
    }

    private static function composerEngagement(BonCommande $bonCommande): string
    {
        $lignes = $bonCommande->lignes->map(function ($ligne) {
            $parts = [$ligne->quantite . ' ' . $ligne->libelle()];

            $details = array_filter([$ligne->couleur, $ligne->matiere]);
            if ($details) {
                $parts[] = implode(', ', $details);
            }

            $parts[] = $ligne->logo ? 'avec logo' : 'sans logo';

            return '- ' . implode(' ', array_filter($parts));
        });

        return $lignes->implode("\n");
    }

    /*
    |--------------------------------------------------------------------------
    | FICHE DU CONTRAT
    |--------------------------------------------------------------------------
    */
    public function show(Programme $programme)
    {
        $programme->load('ecole', 'contrat.bonCommande.lignes.designation', 'echeancesPaiement');

        if (!$programme->contrat) {
            return redirect()->route('programmes.show', $programme)
                ->with('error', "Le contrat se génère automatiquement dès qu'un article est ajouté au premier bon de commande.");
        }

        return view('contrats.show', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | ÉCHÉANCIER DE PAIEMENT (Article 4 du contrat) — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storeEcheance(Request $request, Programme $programme)
    {
        $request->validate([
            'date_prevue'   => 'required|date',
            'montant_prevu' => 'required|numeric|min:0',
        ]);

        $numero = $programme->echeancesPaiement()->max('numero_versement') + 1;

        $programme->echeancesPaiement()->create([
            'numero_versement' => $numero,
            'date_prevue'      => $request->date_prevue,
            'montant_prevu'    => $request->montant_prevu,
        ]);

        return back()->with('success', 'Échéance ajoutée.');
    }

    public function destroyEcheance(EcheancePaiement $echeancePaiement)
    {
        $programme = $echeancePaiement->programme;
        $echeancePaiement->delete();

        // Renumérote proprement (1, 2, 3...) après suppression
        $programme->echeancesPaiement()->orderBy('date_prevue')->get()->values()
            ->each(fn($e, $i) => $e->update(['numero_versement' => $i + 1]));

        return back()->with('success', 'Échéance supprimée.');
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT IMPRIMABLE
    |--------------------------------------------------------------------------
    */
    public function imprimer(Programme $programme)
    {
        $programme->load('ecole', 'contrat.bonCommande', 'echeancesPaiement');

        if (!$programme->contrat) {
            return redirect()->route('programmes.show', $programme)
                ->with('error', "Le contrat n'a pas encore été généré.");
        }

        return view('contrats.imprimer', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLÉTER LES INFOS NON DÉDUCTIBLES DES ARTICLES
    |--------------------------------------------------------------------------
    | (représentant du client, délai de livraison, date de signature — rien
    | de tout cela ne peut être déduit automatiquement des articles commandés)
    */
    public function update(Request $request, Programme $programme)
    {
        $request->validate([
            'representant_client'      => 'nullable|string|max:255',
            'representant_client_role' => 'nullable|string|max:255',
            'date_limite_livraison'    => 'nullable|date',
            'delai_livraison_texte'    => 'nullable|string|max:255',
            'date_signature'           => 'nullable|date',
        ]);

        $programme->contrat->update($request->only(
            'representant_client',
            'representant_client_role',
            'date_limite_livraison',
            'delai_livraison_texte',
            'date_signature'
        ));

        return back()->with('success', 'Contrat mis à jour.');
    }

    public function marquerSigne(Programme $programme)
    {
        $programme->contrat->update(['statut' => 'signe']);
        return back()->with('success', 'Contrat marqué comme signé.');
    }
}