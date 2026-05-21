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
    'ferie_paye',
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
            'initiales'   => strtoupper(substr($e->prenom, 0, 1) . substr($e->nom, 0, 1)),
        ])->values();

        return view('pointages.index', compact('employes', 'pointagesDuJour', 'date', 'stats', 'employesJson'));
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

        return back()->with('success', "✓ Départ de {$employe->prenom} {$employe->nom} enregistré — {$pointage->duree_formattee} travaillées.");
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
                'message' => "✓ Départ de {$employe->prenom} {$employe->nom} enregistré — {$pointage->duree_formattee} travaillées.",
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
    'ferie_paye',
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
    'ferie_paye',
])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | Générer les absences
        |--------------------------------------------------------------------------
        */
        $absences = collect();

        foreach ($joursTravailEntreprise as $date) {

            $dateFormat = Carbon::parse($date)->format('Y-m-d');

            if (!in_array($dateFormat, $datesPresence)) {

                $absences->push((object) [
                    'date' => Carbon::parse($date),
                    'heure_arrivee' => null,
                    'heure_depart' => null,
                    'salaire_jour' => 0,
                    'statut' => 'absent',
                    'retard' => false,
                    'minutes_retard' => 0,
                    'badge_statut' => [
                        'label' => 'Absent',
                        'bg' => '#FEE2E2',
                        'color' => '#B91C1C',
                    ],
                ]);
            }
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
    'ferie_paye',
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

                return [
                    'employe'        => $employe,

                    'jours_presents' => $joursPresents,

                    'jours_absents'  => max(
                        0,
                        $joursTravailEmploye - $joursPresents
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

        $existant = Pointage::where('employe_id', $employe->id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existant) {
            return back()->with('error', "{$employe->prenom} {$employe->nom} est déjà pointé pour cette date.");
        }

        Pointage::create([
            'employe_id'  => $employe->id,
            'date'        => $request->date,
            'statut'      => 'ferie_paye',
            'type'        => 'manuel',
            'salaire_jour'=> $employe->salaire,
            'retard'      => false,
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', "✓ {$employe->prenom} {$employe->nom} — Férié Payé enregistré.");
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
}