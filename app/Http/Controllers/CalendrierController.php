<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Evenement;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $mois = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $date = Carbon::create($annee, $mois, 1);

        $debutMois = $date->copy()->startOfMonth();
        $finMois   = $date->copy()->endOfMonth();

        // Calendrier complet (grille propre)
        $debutCalendrier = $debutMois->copy()->startOfWeek(Carbon::MONDAY);
        $finCalendrier   = $finMois->copy()->endOfWeek(Carbon::SUNDAY);

        // 🔥 ON REMPLACE FERIÉS PAR ÉVÉNEMENTS
        $evenements = Evenement::whereBetween('date', [
                $debutCalendrier,
                $finCalendrier
            ])
            ->get()
            ->groupBy(fn($e) => $e->date->format('Y-m-d'));

        $jours = [];

        for ($jour = $debutCalendrier->copy(); $jour <= $finCalendrier; $jour->addDay()) {

            $dateKey = $jour->format('Y-m-d');

            $eventsDuJour = $evenements[$dateKey] ?? collect();

            $jours[] = [
                'date' => $jour->copy(),

                'dans_mois' => $jour->month === $date->month,

                // samedi + dimanche = week-end
                'weekend' => $jour->isSunday(),

                // événements du jour (peut être plusieurs)
                'evenements' => $eventsDuJour,
            ];
        }

        return view('calendrier.index', [
            'jours' => $jours,
            'date' => $date,
        ]);
    }

    public function show($date)
    {
        $date = Carbon::parse($date);

        $evenements = Evenement::whereDate('date', $date)
            ->latest()
            ->get();

        return response()->json([
            'date' => $date->format('d/m/Y'),
            'evenements' => $evenements,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'type' => ['required'],
            'titre' => ['required', 'max:255'],
            'description' => ['nullable'],
        ]);

        Evenement::create([
            'date' => $request->date,
            'type' => $request->type,
            'titre' => $request->titre,
            'description' => $request->description,
            'est_paye' => $request->boolean('est_paye'),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Événement ajouté.');
    }
}