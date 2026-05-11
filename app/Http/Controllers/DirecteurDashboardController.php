<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Employe;
use App\Models\Pointage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirecteurDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // ── Compteurs employés ──────────────────────────────
        $totalEmployes = Employe::where('actif', true)->count();
        $inactifs      = Employe::where('actif', false)->count();

        // ── Pointages du jour ────────────────────────────────
        $pointagesAujourdhui = Pointage::whereDate('date', $today)->get();

        $presents  = $pointagesAujourdhui->whereIn('statut', ['Présent', 'Retard'])->count();
        $retards   = $pointagesAujourdhui->where('statut', 'Retard')->count();
        $conges    = $pointagesAujourdhui->where('statut', 'Congé')->count();
        $absents   = $totalEmployes - $presents - $conges;
        $absents   = max(0, $absents);
        $absentsNonJustifies = $pointagesAujourdhui->where('statut', 'Absent')->count();

        // ── Premières arrivées (5 premières) ─────────────────
        $premieresArrivees = Pointage::with('employe')
            ->whereDate('date', $today)
            ->whereNotNull('arrivee')
            ->orderBy('arrivee', 'asc')
            ->take(5)
            ->get();

        // ── Présence par département ─────────────────────────
        $departements = Employe::where('actif', true)
            ->select('departement')
            ->distinct()
            ->pluck('departement');

        $presenceParDept = $departements->map(function ($dept) use ($today) {
            $total = Employe::where('actif', true)
                ->where('departement', $dept)
                ->count();

            $presents = Pointage::whereDate('date', $today)
                ->whereIn('statut', ['Présent', 'Retard'])
                ->whereHas('employe', fn($q) => $q->where('departement', $dept))
                ->count();

            return [
                'nom'      => $dept,
                'total'    => $total,
                'presents' => $presents,
            ];
        })->sortByDesc('total')->values();

        // ── Absents du jour ───────────────────────────────────
        $employes = Employe::where('actif', true)->get();

        $idPresents = Pointage::whereDate('date', $today)
            ->whereIn('statut', ['Présent', 'Retard', 'Congé'])
            ->pluck('employe_id');

        $absentsAujourdhui = $employes->whereNotIn('id', $idPresents)->take(10);

        return view('dashboard.directeur', compact(
            'totalEmployes',
            'inactifs',
            'presents',
            'retards',
            'absents',
            'conges',
            'absentsNonJustifies',
            'premieresArrivees',
            'presenceParDept',
            'absentsAujourdhui',
        ));
    }
}