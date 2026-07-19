<?php

namespace App\Http\Controllers;

use App\Models\Ecole;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProgrammeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES PROGRAMMES
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $programmes = Programme::with('ecole', 'bonsCommande', 'contrat')
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
    | ENREGISTRER LE PROGRAMME + génère automatiquement le 1er bon de commande
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {

        $request->validate([
            'ecole_id'                => 'nullable|exists:ecoles,id',
            'ecole_nom' => 'nullable|required_without:ecole_id|string|max:255',
            'ecole_adresse'           => 'nullable|string|max:255',
            'ecole_telephone'         => 'nullable|string|max:50',
            'ecole_contact_nom'       => 'nullable|string|max:255',
            'ecole_contact_telephone' => 'nullable|string|max:50',
            'annee_scolaire'          => 'required|string|max:20',
            'nature'                  => 'required|string|max:255',
        ]);

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

            // Premier bon de commande, généré automatiquement (aucune saisie requise)
            $programme->bonsCommande()->create([
                'numero'             => 'BC-' . now()->year . '-0001',
                'date'               => now()->format('Y-m-d'),
                'nature'             => $request->nature,
                'condition_paiement' => null,
                'montant'            => 0,
            ]);

            return $programme;
        });

        return redirect()->route('programmes.bons.index', $programme)
            ->with('success', 'Programme créé. Ajoutez maintenant les articles de la commande.');
    }

    /*
    |--------------------------------------------------------------------------
    | FICHE DÉTAILLÉE D'UN PROGRAMME (timeline : BC + contrat)
    |--------------------------------------------------------------------------
    */
    public function show(Programme $programme)
    {
        $programme->load('ecole', 'contrat', 'bonsCommande.lignes');

        return view('programmes.show', compact('programme'));
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

    
}

