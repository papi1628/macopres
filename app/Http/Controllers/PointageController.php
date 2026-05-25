<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointageController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FEUILLE DE PRÉSENCE DU JOUR
    |--------------------------------------------------------------------------
    | Affiche tous les employés  avec leur statut du jour
    */
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $date = Carbon::parse($date);

        // Tous les employés
        $employes = Employe::orderBy('nom')
            ->get();

        // Pointages du jour
        $pointagesDuJour = Pointage::with('employe')
            ->whereDate('date', $date)
            ->get()
            ->keyBy('employe_id'); // indexé par employe_id pour accès rapide

        // Stats du jour
        $stats = [
            'total'    => $employes->count(),
            'presents' => $pointagesDuJour->whereIn('statut', [
            'present',
            'retard',
        ])->count(),
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
            'initiales'   => strtoupper(
                substr($e->prenom, 0, 1) .
                substr($e->nom, 0, 1)
            ),
        ])->values();

        // Vérifier si la date est un jour férié payé
        $jourFerie = \App\Models\Evenement::whereDate('date', $date)
            ->where('type', 'ferie')
            ->where('est_paye', true)
            ->first();

        return view('pointages.index', compact('employes', 'pointagesDuJour', 'date', 'stats', 'employesJson', 'jourFerie'));
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

        // Vérifier si déjà pointé aujourd'hui
        $existant = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existant) {
            return back()->with('error', "{$employe->prenom} {$employe->nom} est déjà pointé pour cette date.");
        }

        // Créer le pointage
        $pointage = new Pointage([
            'employe_id'    => $employe->id,
            'date'          => $request->date,
            'heure_arrivee' => $request->heure_arrivee . ':00',
            'demi_journee'  => $request->boolean('demi_journee'),
            'type'          => 'manuel',
            'created_by'    => auth()->id(),
        ]);

        // Calculer retard automatiquement
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

        $pointage->heure_depart  = $request->heure_depart . ':00';
        $pointage->demi_journee  = $request->boolean('demi_journee');
        $pointage->load('employe');
        $pointage->calculerHeuresEtSalaire();
        $pointage->save();



        $employe = $pointage->employe;

        return back()->with('success', "✓ Départ de {$employe->prenom} {$employe->nom} enregistré."/*— {$pointage->duree_formattee} travaillées.")*/);
    }

    /*
    |--------------------------------------------------------------------------
    | POINTER PAR QR CODE
    |--------------------------------------------------------------------------
    */
    public function scan()
    {
        return view('pointages.scan');
    }

    public function scannerQr(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Trouver l'employé via son QR code

        $data = json_decode($request->qr_code, true);

        if (!$data || !isset($data['id'], $data['token'])) {
            return response()->json([
                'success' => false,
                'message' => 'QR code invalide.',
            ], 400);
        }

        $employe = Employe::find($data['id']);

        if (!$employe) {
            return response()->json([
                'success' => false,
                'message' => 'Employé introuvable.',
            ], 404);
        }

        $expectedToken = hash_hmac(
            'sha256',
            $employe->qr_code,
            env('APP_KEY')
        );

        if (!hash_equals($expectedToken, $data['token'])) {
            return response()->json([
                'success' => false,
                'message' => 'QR code falsifié.',
            ], 403);
        }


        $maintenant = now();
        $date       = $maintenant->format('Y-m-d');
        $heure      = $maintenant->format('H:i:s');

        // Chercher pointage du jour
        $pointage = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $date)
            ->first();

        if (!$pointage) {
            // Premier scan = arrivée
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
                'message' => "✓ Arrivée de {$employe->prenom} {$employe->nom} enregistrée à " . $maintenant->format('H:i'),
                'retard'  => $pointage->retard,
                'employe' => [
                    'nom'       => $employe->nom,
                    'prenom'    => $employe->prenom,
                    'matricule' => $employe->matricule,
                    'initiales' => strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)),
                ],
                'heure'   => $maintenant->format('H:i'),
                'statut'  => $pointage->statut,
            ]);

        } elseif (!$pointage->heure_depart) {
            // Deuxième scan = départ
            $pointage->heure_depart = $heure;
            $pointage->load('employe');
            $pointage->calculerHeuresEtSalaire();
            $pointage->save();

            return response()->json([
                'success' => true,
                'action'  => 'depart',
                'message' => "✓ Départ de {$employe->prenom} {$employe->nom} enregistré."/* — {$pointage->duree_formattee} travaillées."*/,
                'employe' => [
                    'nom'       => $employe->nom,
                    'prenom'    => $employe->prenom,
                    'matricule' => $employe->matricule,
                    'initiales' => strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)),
                ],
                'heure'            => $maintenant->format('H:i'),
                'heures_travaillees' => $pointage->duree_formattee,
            ]);

        } else {
            // Déjà pointé arrivée ET départ
            return response()->json([
                'success' => false,
                'message' => "{$employe->prenom} {$employe->nom} a déjà été pointé à l'arrivée et au départ aujourd'hui.",
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

        // Filtres
        if ($request->filled('employe_id')) {
            $query->where('employe_id', $request->employe_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date', '<=', $request->date_fin);
        }

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
        $periode = $request->get('periode', 'mois'); // semaine | mois | annee

        $query = Pointage::where('employe_id', $employe->id);

        switch ($periode) {
            case 'semaine':
                $query->cetteSemaine();
                $titre = 'Cette semaine';
                break;
            case 'annee':
                $query->cetteAnnee();
                $titre = 'Cette année';
                break;
            default:
                $query->ceMois();
                $titre = 'Ce mois';
        }

        $pointages = $query->orderBy('date', 'desc')->get();

        // Stats de la période
        // Jours où l'entreprise a réellement travaillé
        $joursEntreprise = Pointage::query();

        switch ($periode) {

            case 'semaine':
                $joursEntreprise->cetteSemaine();
                break;

            case 'annee':
                $joursEntreprise->cetteAnnee();
                break;

            default:
                $joursEntreprise->ceMois();
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Premier pointage de l'employé
        |--------------------------------------------------------------------------
        */
        $premierPointage = Pointage::where('employe_id', $employe->id)
            ->orderBy('date')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Jours travaillés par l'entreprise
        | depuis le premier pointage de l'employé
        |--------------------------------------------------------------------------
        */
        if ($premierPointage) {

            $joursEntreprise->whereDate('date', '>=', $premierPointage->date);

            $totalJoursTravail = $joursEntreprise
                ->pluck('date')
                ->unique()
                ->count();

        } else {

            $totalJoursTravail = 0;
        }

        $joursPresents = $pointages
            ->whereIn('statut', [
                'present',
                'retard',
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Liste complète des jours de travail
        | depuis le premier pointage de l'employé
        |--------------------------------------------------------------------------
        */
        $joursTravailEntreprise = collect();

        if ($premierPointage) {

            $joursTravailEntreprise = Pointage::query()

                // période sélectionnée
                ->when($periode === 'semaine', fn($q) => $q->cetteSemaine())
                ->when($periode === 'mois', fn($q) => $q->ceMois())
                ->when($periode === 'annee', fn($q) => $q->cetteAnnee())

                // depuis le premier pointage de l'employé
                ->whereDate('date', '>=', $premierPointage->date)

                ->pluck('date')
                ->unique()
                ->sort()
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Dates où l'employé est présent
        |--------------------------------------------------------------------------
        */
        $datesPresence = $pointages
            ->whereIn('statut', [
                'present',
                'retard',
            ])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Générer les absences
        |--------------------------------------------------------------------------
        */
        // Générer toutes les lignes manquantes (absences + dimanches + fériés)
        $absences = collect();

        // Récupérer les événements fériés de la période
        $feriesPayes = \App\Models\Evenement::query()
            ->where('type', 'ferie')
            /*->where('est_paye', true)*/
            ->when($periode === 'semaine', fn($q) => $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]))
            ->when($periode === 'mois',    fn($q) => $q->whereMonth('date', now()->month)->whereYear('date', now()->year))
            ->when($periode === 'annee',   fn($q) => $q->whereYear('date', now()->year))
            ->get()
            ->keyBy(fn($e) => $e->date->format('Y-m-d'));

        // Générer les jours de la période
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

        $datesPresence = $pointages
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        for ($jour = $debutPeriode->copy(); $jour->lte($finPeriode) && $jour->lte(today()); $jour->addDay()) {
            $dateStr = $jour->format('Y-m-d');

            // Dimanche → week-end
            if ($jour->isSunday()) {
                $absences->push((object)[
                    'date'          => $jour->copy(),
                    'heure_arrivee' => null,
                    'heure_depart'  => null,
                    'salaire_jour'  => 0,
                    'statut'        => 'dimanche',
                    'retard'        => false,
                    'minutes_retard'=> 0,
                    'badge_statut'  => ['label' => 'Week-end', 'bg' => '#F1F5F9', 'color' => '#64748B'],
                ]);
                continue;
            }

            // Déjà pointé → skip
            if (in_array($dateStr, $datesPresence)) continue;

            // Férié payé décréé mais pas encore pointé → afficher comme férié
            if (isset($feriesPayes[$dateStr])) {
                $absences->push((object)[
                    'date'          => $jour->copy(),
                    'heure_arrivee' => null,
                    'heure_depart'  => null,
                    'salaire_jour'  => 0,
                    'statut'        => 'ferie_non_pointe',
                    'retard'        => false,
                    'minutes_retard'=> 0,
                    'badge_statut'  => ['label' => '🎉 ' . $feriesPayes[$dateStr]->titre, 'bg' => '#DBEAFE', 'color' => '#1D4ED8'],
                ]);
                continue;
            }

            // Sinon → absent
            $absences->push((object)[
                'date'          => $jour->copy(),
                'heure_arrivee' => null,
                'heure_depart'  => null,
                'salaire_jour'  => 0,
                'statut'        => 'absent',
                'retard'        => false,
                'minutes_retard'=> 0,
                'badge_statut'  => ['label' => 'Absent', 'bg' => '#FCEBEB', 'color' => '#A32D2D'],
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Fusion présences + absences
        |--------------------------------------------------------------------------
        */
        $lignes = $pointages
            ->concat($absences)
            ->sortByDesc('date')
            ->values();   
            
        $lignes->transform(function ($ligne) {

            $dateStr = Carbon::parse($ligne->date)->format('Y-m-d');

            $ferie = \App\Models\Evenement::whereDate('date', $dateStr)
                ->where('type', 'ferie')
                ->first();

            $ligne->est_ferie = $ferie ? true : false;
            $ligne->ferie_titre = $ferie?->titre;

            // Déterminer statut affiché
            if ($ferie) {

                if (($ligne->est_ferie_paye ?? null) === true) {
                    $ligne->statut_affiche = 'ferie_paye';
                    $ligne->libelle_statut = 'Férié payé';
                } else {
                    $ligne->statut_affiche = 'ferie_non_paye';
                    $ligne->libelle_statut = 'Férié non payé';
                }

            } else {

                $ligne->statut_affiche = $ligne->statut ?? 'absent';
                $ligne->libelle_statut = $ligne->badge_statut['label'] ?? '–';
            }

            return $ligne;
        });

        $lignes->transform(function ($ligne) {

            if ($ligne->est_ferie ?? false) {

                $ligne->salaire_affiche = ($ligne->statut === 'ferie_paye')
                    ? ($ligne->salaire_jour ? number_format($ligne->salaire_jour, 0, ',', ' ') . ' F' : '–')
                    : '–';

            } else {

                $ligne->salaire_affiche = $ligne->salaire_jour
                    ? number_format($ligne->salaire_jour, 0, ',', ' ') . ' F'
                    : '–';
            }

            return $ligne;
        });

        $stats = [
            'jours_presents'    => $joursPresents,

            'jours_absents'     => max(
                0,
                $totalJoursTravail - $joursPresents
            ),

            'jours_retard'      => $pointages->where('statut', 'retard')->count(),

            'heures_total'      => round($pointages->sum('heures_travaillees'), 2),

            'salaire_periode'   => round($pointages->sum('salaire_jour'), 2),

            'salaire_mensuel'   => $employe->salaire,
        ];

        return view('pointages.fiche-employe', compact('employe', 'pointages', 'lignes', 'stats', 'periode', 'titre'));
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

        // Stats globales du mois
        $pointagesMois = Pointage::with('employe')
            ->whereMonth('date', $moisNum)
            ->whereYear('date', $annee)
            ->get();


        $statsGlobales = [
            'total_pointages' => $pointagesMois->count(),
            'total_presents'  => $pointagesMois->whereIn('statut', [
            'present',
            'retard',
        ])->count(),
            'total_absents'   => 0, // recalculé après
            'total_retards'   => $pointagesMois->where('statut', 'retard')->count(),
            'total_heures'    => round($pointagesMois->sum('heures_travaillees'), 2),
            'total_salaires'  => round($pointagesMois->sum('salaire_jour'), 2),
            'total_jours_travailles' => $pointagesMois
                ->pluck('date')
                ->unique()
                ->count(),
        ];
        
        $totalJoursTravail = $statsGlobales['total_jours_travailles'];

        // Stats par employé
        $employes = Employe::orderBy('nom')
            ->get()
            ->map(function ($employe) use ($moisNum, $annee, $totalJoursTravail) {
                $pts = Pointage::where('employe_id', $employe->id)
                    ->whereMonth('date', $moisNum)
                    ->whereYear('date', $annee)
                    ->get();

                $joursPresents = $pts->count();

                /*
                |--------------------------------------------------------------------------
                | Premier pointage de l'employé
                |--------------------------------------------------------------------------
                */
                $premierPointage = Pointage::where('employe_id', $employe->id)
                    ->orderBy('date')
                    ->first();

                /*
                |--------------------------------------------------------------------------
                | Calcul des jours travaillés par l'entreprise
                | depuis le premier pointage de l'employé
                |--------------------------------------------------------------------------
                */
                $joursTravailEmploye = 0;

                if ($premierPointage) {

                    $joursTravailEmploye = Pointage::whereDate('date', '>=', $premierPointage->date)
                        ->whereMonth('date', $moisNum)
                        ->whereYear('date', $annee)
                        ->pluck('date')
                        ->unique()
                        ->count();
                }
                /*
                |--------------------------------------------------------------------------
                | Calcul des jours fériés payés de l'entreprise
                | depuis le premier pointage de l'employé
                |--------------------------------------------------------------------------
                */

                $feries = \App\Models\Evenement::where('type', 'ferie')
                    ->where('est_paye', true)
                    ->whereMonth('date', $moisNum)
                    ->whereYear('date', $annee)
                    ->when($premierPointage, function ($q) use ($premierPointage) {
                        $q->whereDate('date', '>=', $premierPointage->date);
                    })
                    ->pluck('date')
                    ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'))
                    ->unique()
                    ->count();


                $joursTravailEmploye = 0;

                if ($premierPointage) {

                    $joursTravailEmploye = Pointage::whereDate('date', '>=', $premierPointage->date)
                        ->whereMonth('date', $moisNum)
                        ->whereYear('date', $annee)
                        ->pluck('date')
                        ->unique()
                        ->count();
                }

                return [
                    'employe'        => $employe,

                    'jours_presents' => $joursPresents,

                    'jours_absents'  => max(
                        0,
                        $joursTravailEmploye - $joursPresents - $feries
                    ),
                    'retards'        => $pts->where('statut', 'retard')->count(),
                    'heures_total'   => round($pts->sum('heures_travaillees'), 2),
                    'salaire_du'     => round($pts->sum('salaire_jour'), 2),
                    'salaire_base'   => $employe->salaire,
                ];
            });

        // Recalculer total absences globales
        $statsGlobales['total_absents'] = $employes->sum('jours_absents');

        return view('pointages.statistiques', compact('statsGlobales', 'employes', 'mois'));
    }

    public function pointerFP(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
            'date'       => 'required|date',
        ]);

        $employe = Employe::findOrFail($request->employe_id);

        // Interdire dimanche
        if (Carbon::parse($request->date)->isSunday()) {
            return back()->with(
                'error',
                'Impossible d’attribuer un férié payé un dimanche.'
            );
        }

        $existant = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existant) {

            if ($existant->est_ferie_paye) {
                return back()->with(
                    'error',
                    "{$employe->prenom} {$employe->nom} possède déjà un férié payé."
                );
            }

            return back()->with(
                'error',
                "{$employe->prenom} {$employe->nom} est déjà pointé pour cette date."
            );
        }


        Pointage::create([
            'employe_id'      => $employe->id,
            'date'            => $request->date,
            'statut'          => 'absent', 
            'type'            => 'manuel',

            'est_ferie_paye'  => true,

            'salaire_jour'    => round($employe->salaire / 30, 2),
            'retard'          => false,
            'created_by'      => auth()->id(),
        ]);

        return back()->with(
            'success',
            "✓ {$employe->prenom} {$employe->nom} — Férié Payé enregistré."
        );
    }
    /*
    |--------------------------------------------------------------------------
    | SUPPRIMER UN POINTAGE (correction)
    |--------------------------------------------------------------------------
    */
    public function destroy(Pointage $pointage)
    {
        $employe = $pointage->employe;
        $pointage->delete();

        return back()->with('success', "Pointage de {$employe->prenom} {$employe->nom} supprimé.");
    }

   public function derniersPointages(Employe $employe, Request $request)
    {
        $jours = collect();

        $baseDate = $request->date
            ? Carbon::parse($request->date)
            : now();

        // 5 jours AVANT la date choisie (on exclut le jour même)
        for ($i = 1; $i <= 5; $i++) {

            $date = $baseDate->copy()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            // Dimanche
            if ($date->isSunday()) {
                $jours->push([
                    'date'   => $dateStr,
                    'statut' => 'weekend',
                ]);
                continue;
            }

            // Pointage existant
            $pointage = Pointage::where('employe_id', $employe->id)
                ->whereDate('date', $dateStr)
                ->first();

            if ($pointage) {
                $jours->push([
                    'id'     => $pointage->id,
                    'date'   => $dateStr,
                    'statut' => $pointage->statut,
                ]);
                continue;
            }

            // Férié payé
            $ferie = \App\Models\Evenement::whereDate('date', $dateStr)
                ->where('type', 'ferie')
                ->where('est_paye', true)
                ->first();

            if ($ferie) {
                $jours->push([
                    'date'            => $dateStr,
                    'statut'          => 'ferie_global',
                    'evenement_titre' => $ferie->titre,
                ]);
                continue;
            }

            // absent
            $jours->push([
                'date'   => $dateStr,
                'statut' => 'absent',
            ]);
        }

        return response()->json($jours->values());
    }
}