<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Ferie;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $mois = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $date = Carbon::create($annee, $mois, 1);

        $debutMois = $date->copy()->startOfMonth();
        $finMois   = $date->copy()->endOfMonth();

        $jours = [];

        // Début et fin du vrai calendrier
        $debutCalendrier = $debutMois->copy()->startOfWeek(Carbon::MONDAY);
        $finCalendrier   = $finMois->copy()->endOfWeek(Carbon::SUNDAY);

        // Fériés du calendrier affiché
        $feries = Ferie::whereBetween('date', [$debutCalendrier, $finCalendrier])
            ->get()
            ->keyBy(fn($f) => $f->date->format('Y-m-d'));

        // Génération des jours
        for ($jour = $debutCalendrier->copy(); $jour <= $finCalendrier; $jour->addDay()) {

            $dateKey = $jour->format('Y-m-d');

            $ferie = $feries[$dateKey] ?? null;

            $jours[] = [
                'date' => $jour->copy(),

                'dans_mois' => $jour->month === $date->month,

                'weekend' => $jour->isWeekend(),

                'ferie' => $ferie,
            ];
        }

        return view('calendrier.index', [
            'jours' => $jours,
            'date' => $date,
        ]);
    }
}