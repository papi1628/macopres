<?php

namespace App\Http\Controllers;

use App\Models\ArticleProduction;
use App\Models\BonCommande;
use App\Models\Contrat;
use App\Models\EcheancePaiement;
use App\Models\Ecole;
use App\Models\LigneBonCommande;
use App\Models\Livraison;
use App\Models\Paiement;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProgrammeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES PROGRAMMES
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $programmes = Programme::with('ecole', 'bonsCommande', 'paiements', 'contrat')
            ->when($request->filled('annee_scolaire'), fn($q) => $q->where('annee_scolaire', $request->annee_scolaire))
            ->when($request->filled('ecole_id'), fn($q) => $q->where('ecole_id', $request->ecole_id))
            ->when($request->filled('statut'), fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $ecoles          = Ecole::orderBy('nom')->get();
        $anneesScolaires = Programme::select('annee_scolaire')->distinct()->orderByDesc('annee_scolaire')->pluck('annee_scolaire');

        return view('programmes.index', compact('programmes', 'ecoles', 'anneesScolaires'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE DE CRÉATION
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $ecoles = Ecole::orderBy('nom')->get();
        return view('programmes.create', compact('ecoles'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER UN PROGRAMME COMPLET
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'ecole_id'                 => 'nullable|exists:ecoles,id',
            'ecole_nom'                => 'required_without:ecole_id|string|max:255',
            'ecole_adresse'            => 'nullable|string|max:255',
            'ecole_telephone'          => 'nullable|string|max:50',
            'ecole_contact_nom'        => 'nullable|string|max:255',
            'ecole_contact_telephone'  => 'nullable|string|max:50',

            'annee_scolaire'          => 'required|string|max:20',

            'description_engagement' => 'nullable|string',
            'montant_total'          => 'nullable|numeric|min:0',
            'date_limite_livraison'  => 'nullable|date',
            'delai_livraison_texte'  => 'nullable|string|max:255',
            'representant_client'    => 'nullable|string|max:255',
            'date_signature'         => 'nullable|date',

            'bons'                       => 'nullable|array',
            'bons.*.numero'              => 'required_with:bons|string|max:50',
            'bons.*.date'                => 'required_with:bons|date',
            'bons.*.montant'             => 'required_with:bons|numeric|min:0',
            'bons.*.nature'              => 'nullable|string|max:255',
            'bons.*.condition_paiement'  => 'nullable|string|max:255',

            'echeances'                  => 'nullable|array',
            'echeances.*.date_prevue'    => 'required_with:echeances|date',
            'echeances.*.montant_prevu'  => 'required_with:echeances|numeric|min:0',
        ]);

        // Empêche le doublon école + année scolaire avant d'entrer dans la transaction
        if ($request->filled('ecole_id')) {
            $existe = Programme::where('ecole_id', $request->ecole_id)
                ->where('annee_scolaire', $request->annee_scolaire)
                ->exists();

            if ($existe) {
                return back()->withInput()->with('error', 'Un programme existe déjà pour cette école et cette année scolaire.');
            }
        }

        $programme = DB::transaction(function () use ($request) {
            $ecole = $request->filled('ecole_id')
                ? Ecole::find($request->ecole_id)
                : Ecole::create([
                    'nom'               => $request->ecole_nom,
                    'adresse'           => $request->ecole_adresse,
                    'telephone'         => $request->ecole_telephone,
                    'contact_nom'       => $request->ecole_contact_nom,
                    'contact_telephone' => $request->ecole_contact_telephone,
                    'created_by'        => Auth::id(),
                ]);

            $programme = Programme::create([
                'ecole_id'       => $ecole->id,
                'annee_scolaire' => $request->annee_scolaire,
                'statut'         => 'en_cours',
                'created_by'     => Auth::id(),
            ]);

            Contrat::create([
                'programme_id'            => $programme->id,
                'description_engagement'  => $request->description_engagement,
                'montant_total'           => $request->montant_total,
                'date_limite_livraison'   => $request->date_limite_livraison,
                'delai_livraison_texte'   => $request->delai_livraison_texte,
                'representant_client'     => $request->representant_client,
                'date_signature'          => $request->date_signature,
            ]);

            foreach ($request->input('bons', []) as $bon) {
                BonCommande::create([
                    'programme_id'       => $programme->id,
                    'numero'             => $bon['numero'],
                    'date'               => $bon['date'],
                    'montant'            => $bon['montant'],
                    'nature'             => $bon['nature'] ?? null,
                    'condition_paiement' => $bon['condition_paiement'] ?? null,
                ]);
            }

            foreach ($request->input('echeances', []) as $i => $ech) {
                EcheancePaiement::create([
                    'programme_id'      => $programme->id,
                    'numero_versement'  => $i + 1,
                    'date_prevue'       => $ech['date_prevue'],
                    'montant_prevu'     => $ech['montant_prevu'],
                ]);
            }

            return $programme;
        });

        return redirect()->route('programmes.show', $programme)->with('success', 'Programme enregistré avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | FICHE DÉTAILLÉE D'UN PROGRAMME
    |--------------------------------------------------------------------------
    */
    public function show(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.lignes.designation');
        $designations = \App\Models\Designation::orderBy('nom')->get();

        return view('programmes.show', compact('programme', 'designations'));
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UN PROGRAMME (cascade sur toutes ses sous-parties)
    |--------------------------------------------------------------------------
    */
    public function destroy(Programme $programme)
    {
        $programme->delete();
        return redirect()->route('programmes.index')->with('success', 'Programme supprimé.');
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGER LE STATUT
    |--------------------------------------------------------------------------
    */
    public function updateStatut(Request $request, Programme $programme)
    {
        $request->validate(['statut' => 'required|in:en_cours,termine,annule']);
        $programme->update(['statut' => $request->statut]);
        return back()->with('success', 'Statut mis à jour.');
    }

    /*
    |--------------------------------------------------------------------------
    | BONS DE COMMANDE — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storeBonCommande(Request $request, Programme $programme)
    {
        // Le système génère la commande automatiquement — l'utilisateur clique juste "Nouvelle commande".
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

    public function updateConditionPaiement(Request $request, BonCommande $bonCommande)
    {
        $request->validate(['condition_paiement' => 'nullable|string|max:255']);
        $bonCommande->update(['condition_paiement' => $request->condition_paiement]);
        return back()->with('success', 'Condition de paiement mise à jour.');
    }

    public function destroyBonCommande(BonCommande $bonCommande)
    {
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

        if (!$bonCommande->programme->contrat) {
            Contrat::create([
                'programme_id'    => $bonCommande->programme_id,
                'bon_commande_id' => $bonCommande->id,
                'statut'          => 'brouillon',
            ]);
        }
        return back()->with('success', 'Article ajouté au bon de commande.');
    }

    public function destroyLigneBonCommande(LigneBonCommande $ligneBonCommande)
    {
        $bonCommande = $ligneBonCommande->bonCommande;
        $ligneBonCommande->delete();
        $bonCommande->recalculerMontant();

        return back()->with('success', 'Article supprimé du bon de commande.');
    }

    /*
    |--------------------------------------------------------------------------
    | ÉCHÉANCES DE PAIEMENT — ajout / suppression
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
        $echeancePaiement->delete();
        return back()->with('success', 'Échéance supprimée.');
    }

    /*
    |--------------------------------------------------------------------------
    | PAIEMENTS (encaissements réels) — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storePaiement(Request $request, Programme $programme)
    {
        $request->validate([
            'date'          => 'required|date',
            'montant'       => 'required|numeric|min:0',
            'mode_paiement' => 'required|in:cheque,virement,wave,orange_money,espece,agent_mandate',
            'reference'     => 'nullable|string|max:255',
        ]);

        $programme->paiements()->create([
            'date'          => $request->date,
            'montant'       => $request->montant,
            'mode_paiement' => $request->mode_paiement,
            'reference'     => $request->reference,
            'recu_par'      => Auth::id(),
        ]);

        return back()->with('success', 'Paiement enregistré.');
    }

    public function destroyPaiement(Paiement $paiement)
    {
        $paiement->delete();
        return back()->with('success', 'Paiement supprimé.');
    }

    /*
    |--------------------------------------------------------------------------
    | ARTICLES DE PRODUCTION — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storeArticle(Request $request, Programme $programme)
    {
        $request->validate([
            'designation' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantite'    => 'nullable|integer|min:0',
            'photo'       => 'nullable|image|max:4096',
        ]);

        $data = $request->only('designation', 'description', 'quantite');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('production', 'public');
        }

        $programme->articlesProduction()->create($data);

        return back()->with('success', 'Article ajouté à la fiche de production.');
    }

    public function destroyArticle(ArticleProduction $articleProduction)
    {
        if ($articleProduction->photo) {
            Storage::disk('public')->delete($articleProduction->photo);
        }
        $articleProduction->delete();
        return back()->with('success', 'Article supprimé.');
    }

    /*
    |--------------------------------------------------------------------------
    | LIVRAISONS — ajout / suppression
    |--------------------------------------------------------------------------
    */
    public function storeLivraison(Request $request, Programme $programme)
    {
        $request->validate([
            'date'            => 'required|date',
            'livreur'         => 'nullable|string|max:255',
            'receptionniste'  => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'quantite'        => 'nullable|integer|min:0',
        ]);

        $programme->livraisons()->create($request->only(
            'date', 'livreur', 'receptionniste', 'description', 'quantite'
        ));

        return back()->with('success', 'Livraison enregistrée.');
    }

    public function destroyLivraison(Livraison $livraison)
    {
        $livraison->delete();
        return back()->with('success', 'Livraison supprimée.');
    }
}