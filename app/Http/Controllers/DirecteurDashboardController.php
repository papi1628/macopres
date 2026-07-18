<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employe;
use App\Models\Pointage;
use App\Models\BonCommande;
use App\Models\EcheancePaiement;
use App\Models\Ecole;
use App\Models\Paiement;
use App\Models\Programme;

class DirecteurDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | EMPLOYÉS
        |--------------------------------------------------------------------------
        */

        $totalEmployes = Employe::count();

        /*
        |--------------------------------------------------------------------------
        | POINTAGES DU JOUR
        |--------------------------------------------------------------------------
        */

        $pointagesAujourdhui = Pointage::with('employe')
            ->whereDate('date', $today)
            ->get();

        $presents = $pointagesAujourdhui
            ->whereIn('statut', [
    'present',
    'retard',
    'ferie_paye',
])
            ->count();

        $retards = $pointagesAujourdhui
            ->where('statut', 'retard')
            ->count();

        

        $absentsNonJustifies = $pointagesAujourdhui
            ->where('statut', 'Absent')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | ABSENTS
        |--------------------------------------------------------------------------
        */

        $idPresents = $pointagesAujourdhui
            ->whereIn('statut', ['present', 'retard', 'absent'])
            ->pluck('employe_id');

        $absentsAujourdhui = Employe::whereNotIn('id', $idPresents)
            ->take(10)
            ->get();

        $absents = $absentsAujourdhui->count();

        /*
        |--------------------------------------------------------------------------
        | PREMIÈRES ARRIVÉES
        |--------------------------------------------------------------------------
        */

        $premieresArrivees = Pointage::with('employe')
            ->whereDate('date', $today)
            ->whereNotNull('heure_arrivee')
            ->orderBy('heure_arrivee', 'asc')
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PRÉSENCE PAR DÉPARTEMENT
        |--------------------------------------------------------------------------
        */

        $departements = Employe::select('departement')
            ->distinct()
            ->pluck('departement');

        $presenceParDept = $departements->map(function ($dept) use ($today) {

            $total = Employe::where('departement', $dept)
                ->count();

            $presents = Pointage::whereDate('date', $today)
                ->whereIn('statut', [
    'present',
    'retard',
    'ferie_paye',
])
                ->whereHas('employe', function ($query) use ($dept) {
                    $query->where('departement', $dept);
                })
                ->count();

            return [
                'nom' => $dept,
                'total' => $total,
                'presents' => $presents,
                'absents' => max(0, $total - $presents),
            ];
        })->sortByDesc('total')->values();

        /*
        |--------------------------------------------------------------------------
        | COMMERCIAL — PROGRAMMES / ÉCOLES / PAIEMENTS
        |--------------------------------------------------------------------------
        */

        $programmesActifs     = Programme::where('statut', 'en_cours')->count();
        $ecolesClientes        = Ecole::count();
        $montantTotalCommande  = BonCommande::sum('montant');
        $montantTotalPaye      = Paiement::sum('montant');
        $resteAPayerGlobal     = $montantTotalCommande - $montantTotalPaye;

        $derniersProgrammes = Programme::with('ecole')
            ->withSum('bonsCommande as montant_total', 'montant')
            ->latest()
            ->take(5)
            ->get();

        $echeancesAVenir = EcheancePaiement::with('programme.ecole')
            ->whereBetween('date_prevue', [$today, $today->copy()->addDays(30)])
            ->orderBy('date_prevue')
            ->take(5)
            ->get();

        $echeancesEnRetard = EcheancePaiement::with('programme.ecole')
            ->where('date_prevue', '<', $today)
            ->orderBy('date_prevue')
            ->get()
            ->filter(fn($ech) => $ech->programme && $ech->programme->solde() > 0)
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view('dashboard.directeur', compact(
            'totalEmployes',
            'presents',
            'retards',
            'absents',
            'absentsNonJustifies',
            'premieresArrivees',
            'presenceParDept',
            'absentsAujourdhui',
            'programmesActifs',
            'ecolesClientes',
            'montantTotalCommande',
            'montantTotalPaye',
            'resteAPayerGlobal',
            'derniersProgrammes',
            'echeancesAVenir',
            'echeancesEnRetard',
        ));
    }
}