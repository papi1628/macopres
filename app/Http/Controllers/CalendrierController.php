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

        $jours = [];
        for ($jour = $debutCalendrier->copy(); $jour <= $finCalendrier; $jour->addDay()) {
            $dateKey = $jour->format('Y-m-d');
            $jours[] = [
                'date'       => $jour->copy(),
                'dans_mois'  => $jour->month === $date->month,
                'weekend'    => $jour->isSunday(),
                'evenements' => $evenements[$dateKey] ?? collect(),
            ];
        }

        return view('calendrier.index', compact('jours', 'date'));
    }

    public function store(Request $request)
    {
        // Seul le directeur peut créer
        abort_if(auth()->user()->role !== 'directeur', 403);

        $request->validate([
            'date'        => ['required', 'date'],
            'type'        => ['required', 'in:ferie,repos,evenement'],
            'titre'       => ['required', 'max:255'],
            'description' => ['nullable', 'string'],
            'est_paye'    => ['nullable'],
        ]);

        Evenement::create([
            'date'        => $request->date,
            'type'        => $request->type,
            'titre'       => $request->titre,
            'description' => $request->description,
            'est_paye'    => $request->boolean('est_paye'),
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', "Férié « {$request->titre} » ajouté au calendrier.");
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

        return back()->with('success', "Férié « {$request->titre} » mis à jour.");
    }

    public function destroy(Evenement $calendrier)
    {
        abort_if(auth()->user()->role !== 'directeur', 403);

        $titre = $calendrier->titre;
        $calendrier->delete();

        return back()->with('success', "Férié « {$titre} » supprimé.");
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
}
