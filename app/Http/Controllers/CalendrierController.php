<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

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

        for ($jour = $debutMois->copy(); $jour <= $finMois; $jour->addDay()) {

            $jours[] = [
                'date' => $jour->copy(),
                'weekend' => $jour->isWeekend(),
            ];
        }

        return view('calendrier.index', [
            'jours' => $jours,
            'date' => $date,
        ]);
    }
}