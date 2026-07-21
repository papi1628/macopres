<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Evenement;

class CalendrierController extends Controller
{
    public function index(Request $request)
    {
        $mois  = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $date = Carbon::create($annee, $mois, 1);

        $debutCalendrier = $date->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $finCalendrier   = $date->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $evenements = Evenement::whereBetween('date', [$debutCalendrier, $finCalendrier])
            ->get()
            ->groupBy(fn($e) => $e->date->format('Y-m-d'));

        $calendrier = $this->genererJours($mois,$annee);


        return view('calendrier.index', [
            'jours' => $calendrier['jours'],
            'date' => $date
        ]);
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'directeur', 403);


        $request->validate([
            'date'        => ['required', 'date'],
            'type'        => ['required', 'in:ferie,repos,evenement'],
            'titre'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'est_paye'    => ['nullable'],
        ]);


        $evenement = Evenement::create([
            'date'        => $request->date,
            'type'        => $request->type,
            'titre'       => $request->titre,
            'description' => $request->description,
            'est_paye'    => $request->boolean('est_paye'),
            'created_by'  => auth()->id(),
        ]);


        return response()->json([
            'success' => true,
            'event' => $evenement
        ]);
    }

    public function update(Request $request, Evenement $calendrier)
    {
        abort_if(auth()->user()->role !== 'directeur', 403);


        $request->validate([
            'date'        => ['required', 'date'],
            'type'        => ['required', 'in:ferie,repos,evenement'],
            'titre'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'est_paye'    => ['nullable'],
        ]);


        $calendrier->update([
            'date'        => $request->date,
            'type'        => $request->type,
            'titre'       => $request->titre,
            'description' => $request->description,
            'est_paye'    => $request->boolean('est_paye'),
        ]);


        return response()->json([
            'success' => true,
            'event' => $calendrier
        ]);
    }

    public function destroy(Evenement $calendrier)
    {
        abort_if(auth()->user()->role !== 'directeur', 403);


        $calendrier->delete();


        return response()->json([
            'success'=>true
        ]);
    }

    /**
     * API JSON — événements d'un jour (utilisé par le modal pointage FP)
     */
    public function jourFerie($date)
    {
        $evenement = Evenement::whereDate('date', $date)
            ->where('type', 'ferie')
            ->where('est_paye', true)
            ->first();

        return response()->json([
            'est_ferie' => !is_null($evenement),
            'evenement' => $evenement,
        ]);
    }

    public function events(Request $request)
    {
        $mois  = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $date = Carbon::create($annee, $mois, 1);

        $debut = $date->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $fin   = $date->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);


        $evenements = Evenement::whereBetween('date', [$debut, $fin])
            ->get();


        return response()->json([
            'events' => $evenements
        ]);
    }

    public function calendarData(Request $request)
    {
        $mois  = $request->get('mois', now()->month);
        $annee = $request->get('annee', now()->year);

        $date = Carbon::create($annee, $mois, 1);

        $debutCalendrier = $date->copy()
            ->startOfMonth()
            ->startOfWeek(Carbon::MONDAY);

        $finCalendrier = $date->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SUNDAY);


        $evenements = Evenement::whereBetween(
            'date',
            [$debutCalendrier, $finCalendrier]
        )
        ->get();


        return response()->json([
            'mois' => $mois,
            'annee' => $annee,
            'titre' => $date->locale('fr')
                ->translatedFormat('F Y'),

            'evenements' => $evenements
        ]);
    }

    private function genererJours($mois, $annee)
    {
        $date = Carbon::create($annee, $mois, 1);

        $debutCalendrier = $date->copy()
            ->startOfMonth()
            ->startOfWeek(Carbon::MONDAY);

        $finCalendrier = $date->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SUNDAY);


        $evenements = Evenement::whereBetween('date', [
            $debutCalendrier,
            $finCalendrier
        ])
        ->get()
        ->groupBy(fn($e) => $e->date->format('Y-m-d'));


        $jours = [];

        for(
            $jour = $debutCalendrier->copy();
            $jour <= $finCalendrier;
            $jour->addDay()
        ){

            $key = $jour->format('Y-m-d');

            $jours[] = [
                'date' => $jour->copy()->format('Y-m-d'),
                'jour' => $jour->day,
                'dans_mois' => $jour->month === $date->month,
                'weekend' => $jour->isWeekend(),
                'dimanche' => $jour->isSunday(),
                'aujourdhui' => $jour->isToday(),
                'evenements' => $evenements[$key] ?? [],
            ];
        }


        return [
            'mois' => $date->locale('fr')->translatedFormat('F Y'),
            'jours' => $jours
        ];
    }

    public function navigation(Request $request)
    {
        return response()->json(
            $this->genererJours(
                $request->mois,
                $request->annee
            )
        );
    }

    
}
