<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Evenement;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STATUTS VALIDES
    |--------------------------------------------------------------------------
    */
    const STATUTS_PRESENTS = ['present', 'retard'];
    const STATUTS_PAYES    = ['present', 'retard', 'ferie_paye'];

    /*
    |--------------------------------------------------------------------------
    | FEUILLE DE PRÉSENCE DU JOUR
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $date = Carbon::parse($request->get('date', today()->format('Y-m-d')));

        $employes = Employe::whereDate('created_at', '<=', $date)
            ->orderBy('nom')
            ->get();

        $pointagesDuJour = Pointage::with('employe')
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employe_id');

        $stats = [
            'total'    => $employes->count(),
            'presents' => $pointagesDuJour->whereIn('statut', self::STATUTS_PRESENTS)->count(),
            'feries'   => $pointagesDuJour->where('statut', 'ferie_paye')->count(),
            'absents'  => $employes->count() - $pointagesDuJour->count(),
            'retards'  => $pointagesDuJour->where('statut', 'retard')->count(),
        ];

        $employesJson = $employes->map(fn($e) => [
            'id'          => $e->id,
            'prenom'      => $e->prenom,
            'nom'         => $e->nom,
            'matricule'   => $e->matricule,
            'departement' => $e->departement,
            'salaire'     => $e->salaire,
            'initiales'   => mb_strtoupper(substr($e->prenom, 0, 1) . substr($e->nom, 0, 1)),
        ])->values();

        $jourFerie = Evenement::whereDate('date', $date)
            ->where('type', 'ferie')
            ->where('est_paye', true)
            ->first();

        return view('pointages.index', compact(
            'employes', 'pointagesDuJour', 'date', 'stats', 'employesJson', 'jourFerie'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | POINTER MANUELLEMENT — ARRIVÉE
    |--------------------------------------------------------------------------
    */
    public function pointer(Request $request)
    {
        $request->validate([
            'employe_id'    => 'required|exists:employes,id',
            'date'          => 'required|date',
            'heure_arrivee' => 'required|date_format:H:i',
            'demi_journee'  => 'nullable|boolean',
        ]);

        $employe = Employe::findOrFail($request->employe_id);

        $existant = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existant) {
            return back()->with('error', "{$employe->prenom} {$employe->nom} est déjà pointé pour cette date.");
        }

        $pointage = new Pointage([
            'employe_id'    => $employe->id,
            'date'          => $request->date,
            'heure_arrivee' => $request->heure_arrivee . ':00',
            'demi_journee'  => $request->boolean('demi_journee'),
            'type'          => 'manuel',
            'created_by'    => auth()->id(),
        ]);

        $pointage->calculerRetard();
        $pointage->save();

        $message = $pointage->retard
            ? "⚠️ {$employe->prenom} {$employe->nom} pointé avec {$pointage->retard_formate} de retard."
            : "✓ {$employe->prenom} {$employe->nom} pointé à {$request->heure_arrivee}.";

        return back()->with('success', $message);
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER LE DÉPART
    |--------------------------------------------------------------------------
    */
    public function enregistrerDepart(Request $request, Pointage $pointage)
    {
        $request->validate([
            'heure_depart' => 'required|date_format:H:i',
            'demi_journee' => 'nullable|boolean',
        ]);

        $pointage->heure_depart = $request->heure_depart . ':00';
        $pointage->demi_journee = $request->boolean('demi_journee');
        $pointage->load('employe');
        $pointage->calculerHeuresEtSalaire();
        $pointage->save();

        return back()->with('success',
            "✓ Départ de {$pointage->employe->prenom} {$pointage->employe->nom} enregistré."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | POINTER FÉRIÉ PAYÉ
    |--------------------------------------------------------------------------
    */
    public function pointerFP(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date'       => 'required|date',
        ]);

        $employe = Employe::findOrFail($request->employe_id);
        $date    = Carbon::parse($request->date);

        $jourFerie = Evenement::whereDate('date', $date)
            ->where('type', 'ferie')
            ->where('est_paye', true)
            ->first();

        if (!$jourFerie) {
            return back()->with('error', "Cette date n'est pas un jour férié payé décréé.");
        }

        if ($date->isSunday()) {
            return back()->with('error', "Impossible d'attribuer un férié payé un dimanche.");
        }

        $existant = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $date)
            ->first();

        if ($existant) {
            if ($existant->statut === 'ferie_paye') {
                return back()->with('error', "{$employe->prenom} {$employe->nom} a déjà son férié payé.");
            }
            return back()->with('error', "{$employe->prenom} {$employe->nom} est déjà pointé ce jour.");
        }

        Pointage::create([
            'employe_id'  => $employe->id,
            'date'        => $date->format('Y-m-d'),
            'statut'      => 'ferie_paye',
            'type'        => 'manuel',
            'salaire_jour'=> $employe->salaire,
            'retard'      => false,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success',
            "✓ {$employe->prenom} {$employe->nom} — Férié Payé ({$jourFerie->titre}) enregistré."
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SCANNER QR CODE
    |--------------------------------------------------------------------------
    */
    public function scan()
    {
        return view('pointages.scan');
    }

    public function scannerQr(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $data = json_decode($request->qr_code, true);

        if (!$data || !isset($data['id'], $data['token'])) {
            return response()->json(['success' => false, 'message' => 'QR code invalide.'], 400);
        }

        $employe = Employe::find($data['id']);

        if (!$employe) {
            return response()->json(['success' => false, 'message' => 'Employé introuvable.'], 404);
        }

        $expectedToken = hash_hmac('sha256', $employe->qr_code, env('APP_KEY'));

        if (!hash_equals($expectedToken, $data['token'])) {
            return response()->json(['success' => false, 'message' => 'QR code falsifié.'], 403);
        }

        $maintenant = now();
        $date       = $maintenant->format('Y-m-d');
        $heure      = $maintenant->format('H:i:s');

        $pointage = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $date)
            ->first();

        if (!$pointage) {
            $pointage = new Pointage([
                'employe_id'    => $employe->id,
                'date'          => $date,
                'heure_arrivee' => $heure,
                'type'          => 'qr_code',
                'created_by'    => auth()->id(),
            ]);
            $pointage->calculerRetard();
            $pointage->save();

            return response()->json([
                'success' => true,
                'action'  => 'arrivee',
                'retard'  => $pointage->retard,
                'employe' => [
                    'nom'       => $employe->nom,
                    'prenom'    => $employe->prenom,
                    'matricule' => $employe->matricule,
                    'initiales' => mb_strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)),
                ],
                'heure'  => $maintenant->format('H:i'),
                'statut' => $pointage->statut,
            ]);

        } elseif (!$pointage->heure_depart) {
            $pointage->heure_depart = $heure;
            $pointage->load('employe');
            $pointage->calculerHeuresEtSalaire();
            $pointage->save();

            return response()->json([
                'success'            => true,
                'action'             => 'depart',
                'employe'            => [
                    'nom'       => $employe->nom,
                    'prenom'    => $employe->prenom,
                    'matricule' => $employe->matricule,
                    'initiales' => mb_strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)),
                ],
                'heure'              => $maintenant->format('H:i'),
                'heures_travaillees' => $pointage->duree_formattee,
            ]);

        } else {
            return response()->json([
                'success' => false,
                'message' => "{$employe->prenom} {$employe->nom} a déjà été pointé à l'arrivée et au départ.",
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORIQUE GLOBAL
    |--------------------------------------------------------------------------
    */
    public function historique(Request $request)
    {
        $query = Pointage::with('employe')
            ->orderBy('date', 'desc')
            ->orderBy('heure_arrivee', 'desc');

        if ($request->filled('employe_id')) $query->where('employe_id', $request->employe_id);
        if ($request->filled('statut'))     $query->where('statut', $request->statut);
        if ($request->filled('date_debut')) $query->whereDate('date', '>=', $request->date_debut);
        if ($request->filled('date_fin'))   $query->whereDate('date', '<=', $request->date_fin);

        if ($request->filled('mois')) {
            [$annee, $mois] = explode('-', $request->mois);
            $query->whereMonth('date', $mois)->whereYear('date', $annee);
        }

        $pointages = $query->paginate(25)->withQueryString();
        $employes  = Employe::orderBy('nom')->get();

        return view('pointages.historique', compact('pointages', 'employes'));
    }

    /*
    |--------------------------------------------------------------------------
    | FICHE INDIVIDUELLE D'UN EMPLOYÉ
    |--------------------------------------------------------------------------
    */
    public function ficheEmploye(Request $request, Employe $employe)
    {
        $periode = $request->get('periode', 'mois');

        $query = Pointage::where('employe_id', $employe->id);

        switch ($periode) {
            case 'semaine': $query->cetteSemaine(); $titre = 'Cette semaine'; break;
            case 'annee':   $query->cetteAnnee();   $titre = 'Cette année';  break;
            default:        $query->ceMois();        $titre = 'Ce mois';
        }

        $pointages = $query->orderBy('date', 'desc')->get();

        $debutPeriode = match($periode) {
            'semaine' => now()->startOfWeek(),
            'annee'   => now()->startOfYear(),
            default   => now()->startOfMonth(),
        };
        $finPeriode = match($periode) {
            'semaine' => now()->endOfWeek(),
            'annee'   => now()->endOfYear(),
            default   => now()->endOfMonth(),
        };

        // Ne pas prendre en compte les jours avant la création de l'employé
        $dateDebutEmploye = $employe->date_embauche
            ? Carbon::parse($employe->date_embauche)
            : Carbon::parse($employe->created_at);

        if ($dateDebutEmploye->gt($debutPeriode)) {
            $debutPeriode = $dateDebutEmploye;
        }

        // Fériés payés de la période
        $feriesPayes = Evenement::where('type', 'ferie')
            ->where('est_paye', true)
            ->whereBetween('date', [$debutPeriode, $finPeriode])
            ->get()
            ->keyBy(fn($e) => $e->date->format('Y-m-d'));

        // Dates déjà pointées
        $datesPointees = $pointages
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        // Générer les lignes manquantes
        $lignesManquantes = collect();
        $joursOuvrables   = 0;

        for ($jour = $debutPeriode->copy(); $jour->lte($finPeriode) && $jour->lte(today()); $jour->addDay()) {
            $dateStr = $jour->format('Y-m-d');

            if (in_array($dateStr, $datesPointees)) {
                if (!$jour->isSunday()) $joursOuvrables++;
                continue;
            }

            if ($jour->isSunday()) {
                $lignesManquantes->push((object)[
                    'date'          => $jour->copy(),
                    'heure_arrivee' => null,
                    'heure_depart'  => null,
                    'salaire_jour'  => null,
                    'statut'        => 'dimanche',
                    'retard'        => false,
                    'minutes_retard'=> 0,
                    'badge_statut'  => ['label' => 'Week-end', 'bg' => '#F1F5F9', 'color' => '#64748B'],
                ]);
                continue;
            }

            $joursOuvrables++;

            if (isset($feriesPayes[$dateStr])) {
                $lignesManquantes->push((object)[
                    'date'          => $jour->copy(),
                    'heure_arrivee' => null,
                    'heure_depart'  => null,
                    'salaire_jour'  => null,
                    'statut'        => 'ferie_non_pointe',
                    'retard'        => false,
                    'minutes_retard'=> 0,
                    'badge_statut'  => [
                        'label' => 'Férié non payé',
                        'bg'    => '#EFF6FF',
                        'color' => '#1D4ED8',
                    ],
                ]);
                continue;
            }

            $lignesManquantes->push((object)[
                'date'          => $jour->copy(),
                'heure_arrivee' => null,
                'heure_depart'  => null,
                'salaire_jour'  => null,
                'statut'        => 'absent',
                'retard'        => false,
                'minutes_retard'=> 0,
                'badge_statut'  => ['label' => 'Absent', 'bg' => '#FCEBEB', 'color' => '#A32D2D'],
            ]);
        }

        $lignes = $pointages
            ->concat($lignesManquantes)
            ->sortByDesc(fn($l) => Carbon::parse($l->date)->format('Y-m-d'))
            ->values();

        $joursPresents    = $pointages->whereIn('statut', self::STATUTS_PRESENTS)->count();
        $joursFeriesPayes = $pointages->where('statut', 'ferie_paye')->count();
        $joursAbsents     = max(0, $joursOuvrables - $joursPresents - $joursFeriesPayes);
        
        $stats = [
            'jours_presents'     => $joursPresents,
            'jours_feries_payes' => $joursFeriesPayes,
            'jours_absents'      => $joursAbsents,
            'jours_retard'       => $pointages->where('statut', 'retard')->count(),
            'salaire_periode'    => round($pointages->sum('salaire_jour'), 2),
            'salaire_mensuel'    => $employe->salaire,
        ];

        return view('pointages.fiche-employe', compact(
            'employe', 'pointages', 'lignes', 'stats', 'periode', 'titre'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STATISTIQUES GLOBALES
    |--------------------------------------------------------------------------
    */
    public function statistiques(Request $request)
    {
        $mois = $request->get('mois', now()->format('Y-m'));
        [$annee, $moisNum] = explode('-', $mois);

        $pointagesMois = Pointage::with('employe')
            ->whereMonth('date', $moisNum)
            ->whereYear('date', $annee)
            ->get();

        // Jours ouvrables du mois (hors dimanches)
        $debutMois = Carbon::create($annee, $moisNum, 1)->startOfMonth();
        $finMois   = $debutMois->copy()->endOfMonth();
        $joursOuvrablesMois = 0;
        for ($j = $debutMois->copy(); $j->lte($finMois); $j->addDay()) {
            if (!$j->isSunday()) $joursOuvrablesMois++;
        }

        $nombreFeries = Evenement::where('type', 'ferie')
            ->whereMonth('date', $moisNum)
            ->whereYear('date', $annee)
            ->count();

        $statsGlobales = [
            'total_presents'     => $pointagesMois->whereIn('statut', self::STATUTS_PRESENTS)->count(),
            'total_feries_payes' => $pointagesMois->where('statut', 'ferie_paye')->count(),
            'total_absents'      => 0,
            'total_retards'      => $pointagesMois->where('statut', 'retard')->count(),
            'total_salaires'     => round($pointagesMois->sum('salaire_jour'), 2),
            'jours_ouvrables'    => $joursOuvrablesMois,
            'jours_travail'    => max(0, $joursOuvrablesMois - $nombreFeries),
        ];

        $employes = Employe::orderBy('nom')
            ->get()
            ->map(function ($employe) use ($moisNum, $annee, $joursOuvrablesMois, $debutMois, $finMois) {

                $pts = Pointage::where('employe_id', $employe->id)
                    ->whereMonth('date', $moisNum)
                    ->whereYear('date', $annee)
                    ->get();

                $joursPresents    = $pts->whereIn('statut', self::STATUTS_PRESENTS)->count();
                $joursFeriesPayes = $pts->where('statut', 'ferie_paye')->count();

                // Jours ouvrables adaptés à la création de l'employé
                $dateDebutCalcul = $debutMois;

                $dateDebutEmploye = $employe->date_embauche
                    ? Carbon::parse($employe->date_embauche)
                    : Carbon::parse($employe->created_at);

                if ($dateDebutEmploye->gt($dateDebutCalcul)) {
                    $dateDebutCalcul = $dateDebutEmploye;
                }

                $joursOuvrablesEmploye = 0;

                for (
                    $j = $dateDebutCalcul->copy();
                    $j->lte($finMois) && $j->lte(today());
                    $j->addDay()
                ) {
                    if (!$j->isSunday()) {
                        $joursOuvrablesEmploye++;
                    }
                }

                $joursAbsents = max(0, $joursOuvrablesEmploye - $joursPresents - $joursFeriesPayes);

                return [
                    'employe'        => $employe,
                    'jours_presents' => $joursPresents,
                    'jours_feries'   => $joursFeriesPayes,
                    'jours_absents'  => $joursAbsents,
                    'retards'        => $pts->where('statut', 'retard')->count(),
                    'salaire_du'     => round($pts->sum('salaire_jour'), 2),
                    'salaire_base'   => $employe->salaire,
                ];
            });

        $statsGlobales['total_absents'] = $employes->sum('jours_absents');

        return view('pointages.statistiques', compact('statsGlobales', 'employes', 'mois'));
    }

    /*
    |--------------------------------------------------------------------------
    | 5 DERNIERS POINTAGES (API modal FP)
    |--------------------------------------------------------------------------
    */
    public function derniersPointages(Employe $employe, Request $request)
    {
        $baseDate = $request->date ? Carbon::parse($request->date) : now();
        $jours    = collect();

        for ($i = 1; $i <= 5; $i++) {
            $date    = $baseDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            if ($date->isSunday()) {
                $jours->push(['date' => $dateStr, 'statut' => 'weekend']);
                continue;
            }

            $pointage = Pointage::where('employe_id', $employe->id)
                ->whereDate('date', $dateStr)
                ->first();

            if ($pointage) {
                $jours->push(['id' => $pointage->id, 'date' => $dateStr, 'statut' => $pointage->statut]);
                continue;
            }

            $ferie = Evenement::whereDate('date', $dateStr)
                ->where('type', 'ferie')
                ->where('est_paye', true)
                ->first();

            if ($ferie) {
                $jours->push(['date' => $dateStr, 'statut' => 'ferie_global', 'evenement_titre' => $ferie->titre]);
                continue;
            }

            $jours->push(['date' => $dateStr, 'statut' => 'absent']);
        }

        return response()->json($jours->values());
    }

    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UN POINTAGE
    |--------------------------------------------------------------------------
    */
    public function destroy(Pointage $pointage)
    {
        $employe = $pointage->employe;
        $pointage->delete();
        return back()->with('success', "Pointage de {$employe->prenom} {$employe->nom} supprimé.");
    }
}