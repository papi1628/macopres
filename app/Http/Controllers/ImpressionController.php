<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Evenement;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ImpressionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAGE SÉLECTION — filtres + choix des employés
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $employes     = Employe::orderBy('nom')->get();
        $departements = Employe::whereNotNull('departement')
            ->distinct()
            ->pluck('departement')
            ->sort()
            ->values();

        return view('impressions.index', compact('employes', 'departements'));
    }

    /*
    |--------------------------------------------------------------------------
    | APERÇU IMPRIMABLE — toutes les fiches sélectionnées (bulletin présence/salaire)
    |--------------------------------------------------------------------------
    */
    public function apercu(Request $request)
    {
        $request->validate([
            'employe_ids' => 'required|array|min:1',
            'employe_ids.*' => 'exists:employes,id',
            'periode'     => 'required|in:semaine,mois,annee',
            'mois'        => 'nullable|string',
            'annee'       => 'nullable|integer',
        ]);

        $periode = $request->periode;

        [$debutPeriode, $finPeriode, $titrePeriode] = $this->calculerPeriode(
            $periode,
            $request->mois,
            $request->annee
        );

        $feriesPayes = $this->feriesPayesEntre($debutPeriode, $finPeriode);

        $joursOuvrables = $this->joursOuvrablesEntre($debutPeriode, $finPeriode);

        $fiches = collect($request->employe_ids)->map(function ($id) use (
            $debutPeriode, $finPeriode, $feriesPayes, $joursOuvrables
        ) {
            $employe = Employe::find($id);
            if (!$employe) return null;

            return $this->construireFiche($employe, $debutPeriode, $finPeriode, $feriesPayes, $joursOuvrables);
        })->filter()->values();

        return view('impressions.apercu', compact('fiches', 'titrePeriode', 'debutPeriode', 'finPeriode'));
    }

    /*
    |--------------------------------------------------------------------------
    | IMPRESSION FICHE INDIVIDUELLE (bulletin présence/salaire)
    |--------------------------------------------------------------------------
    */
    public function ficheEmploye(Request $request, Employe $employe)
    {
        $periode = $request->get('periode', 'mois');

        [$debutPeriode, $finPeriode, $titrePeriode] = $this->calculerPeriode(
            $periode,
            $request->mois,
            $request->annee
        );

        $feriesPayes    = $this->feriesPayesEntre($debutPeriode, $finPeriode);
        $joursOuvrables = $this->joursOuvrablesEntre($debutPeriode, $finPeriode);

        $fiche  = $this->construireFiche($employe, $debutPeriode, $finPeriode, $feriesPayes, $joursOuvrables);
        $fiches = collect([$fiche]);

        return view('impressions.apercu', compact('fiches', 'titrePeriode', 'debutPeriode', 'finPeriode'));
    }

    /*
    |--------------------------------------------------------------------------
    | BADGES QR — impression carte(s) employé(s)
    |--------------------------------------------------------------------------
    */
    public function badges(Request $request)
    {
        $request->validate([
            'employe_ids'   => 'required|array|min:1',
            'employe_ids.*' => 'exists:employes,id',
            'format'        => 'required|in:unique,planche',
        ]);

        $employes = Employe::whereIn('id', $request->employe_ids)
            ->orderBy('nom')
            ->get();

        return view('impressions.badges', [
            'employes' => $employes,
            'format'   => $request->format,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RAPPORT STATISTIQUE — vue globale + détail par département
    |--------------------------------------------------------------------------
    */
    public function statistiques(Request $request)
    {
        $request->validate([
            'periode'     => 'required|in:semaine,mois,annee',
            'mois'        => 'nullable|string',
            'annee'       => 'nullable|integer',
            'departement' => 'nullable|string',
        ]);

        [$debutPeriode, $finPeriode, $titrePeriode] = $this->calculerPeriode(
            $periode = $request->periode,
            $request->mois,
            $request->annee
        );

        $employesQuery = Employe::query();
        if ($request->filled('departement')) {
            $employesQuery->where('departement', $request->departement);
        }
        $employes = $employesQuery->get();

        $joursOuvrables = $this->joursOuvrablesEntre($debutPeriode, $finPeriode);

        $pointages = Pointage::whereIn('employe_id', $employes->pluck('id'))
            ->whereBetween('date', [$debutPeriode->format('Y-m-d'), $finPeriode->format('Y-m-d')])
            ->get();

        $statsParDepartement = $employes
            ->groupBy(fn($e) => $e->departement ?: 'Non renseigné')
            ->map(function ($groupe, $nomDepartement) use ($pointages, $joursOuvrables) {
                return $this->calculerStatsGroupe($nomDepartement, $groupe, $pointages, $joursOuvrables);
            })
            ->values()
            ->sortBy('departement')
            ->values();

        $globalStats = $this->calculerStatsGroupe('Global', $employes, $pointages, $joursOuvrables);

        return view('impressions.statistiques', compact(
            'globalStats',
            'statsParDepartement',
            'titrePeriode',
            'debutPeriode',
            'finPeriode'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FEUILLE DE PRÉSENCE IMPRIMABLE — une journée ou une plage de dates
    |--------------------------------------------------------------------------
    */
    public function feuillePresence(Request $request)
    {
        $request->validate([
            'date'        => 'required_without:date_debut|nullable|date',
            'date_debut'  => 'required_without:date|nullable|date',
            'date_fin'    => 'nullable|date|after_or_equal:date_debut',
            'departement' => 'nullable|string',
        ]);

        $debut = $request->filled('date_debut')
            ? Carbon::parse($request->date_debut)
            : Carbon::parse($request->date);

        $fin = $request->filled('date_fin')
            ? Carbon::parse($request->date_fin)
            : $debut->copy();

        $employesQuery = Employe::query();
        if ($request->filled('departement')) {
            $employesQuery->where('departement', $request->departement);
        }
        $employes = $employesQuery->orderBy('nom')->get();

        $pointagesParJour = Pointage::whereIn('employe_id', $employes->pluck('id'))
            ->whereBetween('date', [$debut->format('Y-m-d'), $fin->format('Y-m-d')])
            ->with('employe')
            ->get()
            ->groupBy(fn($p) => Carbon::parse($p->date)->format('Y-m-d'));

        $feuilles = collect();

        for ($jour = $debut->copy(); $jour->lte($fin); $jour->addDay()) {
            $dateStr       = $jour->format('Y-m-d');
            $pointagesJour = $pointagesParJour->get($dateStr, collect())->keyBy('employe_id');

            $lignes = $employes->map(function ($employe) use ($pointagesJour, $jour) {
                $pointage = $pointagesJour->get($employe->id);
                if ($pointage) {
                    return $pointage;
                }

                return (object) [
                    'employe_id'     => $employe->id,
                    'employe'        => $employe,
                    'date'           => $jour->copy(),
                    'heure_arrivee'  => null,
                    'heure_depart'   => null,
                    'statut'         => $jour->isSunday() ? 'dimanche' : 'absent',
                    'retard'         => false,
                    'minutes_retard' => 0,
                ];
            })->values();

            $feuilles->push([
                'date'   => $jour->copy(),
                'lignes' => $lignes,
                'stats'  => [
                    'total'    => $employes->count(),
                    'presents' => $lignes->whereIn('statut', ['present', 'retard'])->count(),
                    'absents'  => $lignes->where('statut', 'absent')->count(),
                    'retards'  => $lignes->where('statut', 'retard')->count(),
                ],
            ]);
        }

        return view('impressions.feuille-presence', compact('feuilles', 'debut', 'fin'));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Construire une fiche employé (lignes + stats) sur une période
    |--------------------------------------------------------------------------
    */
    private function construireFiche(Employe $employe, Carbon $debutPeriode, Carbon $finPeriode, $feriesPayes, int $joursOuvrables): array
    {
        $pointages = Pointage::where('employe_id', $employe->id)
            ->whereBetween('date', [$debutPeriode->format('Y-m-d'), $finPeriode->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        $datesPointees = $pointages
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        $lignesManquantes = collect();
        for ($jour = $debutPeriode->copy(); $jour->lte($finPeriode) && $jour->lte(today()); $jour->addDay()) {
            $dateStr = $jour->format('Y-m-d');
            if (in_array($dateStr, $datesPointees)) continue;

            if ($jour->isSunday()) {
                $lignesManquantes->push((object) [
                    'date' => $jour->copy(), 'heure_arrivee' => null, 'heure_depart' => null,
                    'salaire_jour' => null, 'statut' => 'dimanche', 'retard' => false, 'minutes_retard' => 0,
                    'badge_statut' => ['label' => 'Week-end', 'bg' => '#F1F5F9', 'color' => '#64748B'],
                ]);
                continue;
            }

            if (isset($feriesPayes[$dateStr])) {
                $lignesManquantes->push((object) [
                    'date' => $jour->copy(), 'heure_arrivee' => null, 'heure_depart' => null,
                    'salaire_jour' => null, 'statut' => 'ferie_non_pointe', 'retard' => false, 'minutes_retard' => 0,
                    'badge_statut' => ['label' => '🎉 ' . $feriesPayes[$dateStr]->titre, 'bg' => '#EFF6FF', 'color' => '#1D4ED8'],
                ]);
                continue;
            }

            $lignesManquantes->push((object) [
                'date' => $jour->copy(), 'heure_arrivee' => null, 'heure_depart' => null,
                'salaire_jour' => null, 'statut' => 'absent', 'retard' => false, 'minutes_retard' => 0,
                'badge_statut' => ['label' => 'Absent', 'bg' => '#FCEBEB', 'color' => '#A32D2D'],
            ]);
        }

        $lignes = $pointages
            ->concat($lignesManquantes)
            ->sortByDesc(fn($l) => Carbon::parse($l->date)->format('Y-m-d'))
            ->values();

        $joursPresents    = $pointages->whereIn('statut', ['present', 'retard'])->count();
        $joursFeriesPayes = $pointages->where('statut', 'ferie_paye')->count();
        $joursAbsents     = max(0, $joursOuvrables - $joursPresents - $joursFeriesPayes);

        return [
            'employe' => $employe,
            'lignes'  => $lignes,
            'stats'   => [
                'jours_presents'     => $joursPresents,
                'jours_feries_payes' => $joursFeriesPayes,
                'jours_absents'      => $joursAbsents,
                'jours_retard'       => $pointages->where('statut', 'retard')->count(),
                'salaire_periode'    => round($pointages->sum('salaire_jour'), 2),
                'salaire_mensuel'    => $employe->salaire,
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Statistiques agrégées pour un groupe d'employés (global ou département)
    |--------------------------------------------------------------------------
    */
    private function calculerStatsGroupe(string $nom, $groupeEmployes, $pointages, int $joursOuvrables): array
    {
        $ids             = $groupeEmployes->pluck('id');
        $pointagesGroupe = $pointages->whereIn('employe_id', $ids);

        $joursPresents = $pointagesGroupe->whereIn('statut', ['present', 'retard'])->count();
        $joursRetard   = $pointagesGroupe->where('statut', 'retard')->count();
        $joursFeries   = $pointagesGroupe->where('statut', 'ferie_paye')->count();
        $totalAttendu  = $groupeEmployes->count() * $joursOuvrables;
        $joursAbsents  = max(0, $totalAttendu - $joursPresents - $joursFeries);
        $tauxPresence  = $totalAttendu > 0 ? round(($joursPresents / $totalAttendu) * 100, 1) : 0;

        return [
            'departement'     => $nom,
            'nb_employes'      => $groupeEmployes->count(),
            'jours_presents'   => $joursPresents,
            'jours_absents'    => $joursAbsents,
            'jours_retard'     => $joursRetard,
            'jours_feries'     => $joursFeries,
            'taux_presence'    => $tauxPresence,
            'masse_salariale'  => round($pointagesGroupe->sum('salaire_jour'), 2),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Fériés payés entre deux dates, indexés par date (Y-m-d)
    |--------------------------------------------------------------------------
    */
    private function feriesPayesEntre(Carbon $debut, Carbon $fin)
    {
        return Evenement::where('type', 'ferie')
            ->where('est_paye', true)
            ->whereBetween('date', [$debut, $fin])
            ->get()
            ->keyBy(fn($e) => $e->date->format('Y-m-d'));
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Nombre de jours ouvrables (hors dimanche) entre deux dates, borné à aujourd'hui
    |--------------------------------------------------------------------------
    */
    private function joursOuvrablesEntre(Carbon $debut, Carbon $fin): int
    {
        $joursOuvrables = 0;
        for ($j = $debut->copy(); $j->lte($fin) && $j->lte(today()); $j->addDay()) {
            if (!$j->isSunday()) $joursOuvrables++;
        }
        return $joursOuvrables;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER — Calculer les dates selon la période
    |--------------------------------------------------------------------------
    */
    private function calculerPeriode(string $periode, ?string $mois, ?int $annee): array
    {
        $now = now();

        switch ($periode) {
            case 'semaine':
                $debut  = $now->copy()->startOfWeek();
                $fin    = $now->copy()->endOfWeek();
                $titre  = 'Semaine du ' . $debut->locale('fr')->isoFormat('D MMM') . ' au ' . $fin->locale('fr')->isoFormat('D MMM YYYY');
                break;

            case 'annee':
                $anneeVal = $annee ?? $now->year;
                $debut    = Carbon::create($anneeVal, 1, 1)->startOfYear();
                $fin      = Carbon::create($anneeVal, 12, 31)->endOfYear();
                $titre    = 'Année ' . $anneeVal;
                break;

            default: // mois
                if ($mois) {
                    [$y, $m] = explode('-', $mois);
                    $debut   = Carbon::create($y, $m, 1)->startOfMonth();
                } else {
                    $debut = $now->copy()->startOfMonth();
                }
                $fin   = $debut->copy()->endOfMonth();
                $titre = ucfirst($debut->locale('fr')->translatedFormat('F Y'));
                break;
        }

        return [$debut, $fin, $titre];
    }
}