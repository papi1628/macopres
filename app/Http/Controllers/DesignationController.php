<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::orderBy('nom')->get();
        return view('designations.index', compact('designations'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'directeur') {
            abort(403);
        }

        $request->validate([
            'nom'         => 'required|string|max:255',
            'manche'      => 'required|in:courte,longue,sans_objet',
            'prix_defaut' => 'required|numeric|min:0',
        ]);

        Designation::create([
            'nom'         => $request->nom,
            'manche'      => $request->manche,
            'prix_defaut' => $request->prix_defaut,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Désignation ajoutée au catalogue.');
    }

    public function update(Request $request, Designation $designation)
    {
        if (Auth::user()->role !== 'directeur') {
            abort(403);
        }

        $request->validate([
            'nom'         => 'required|string|max:255',
            'manche'      => 'required|in:courte,longue,sans_objet',
            'prix_defaut' => 'required|numeric|min:0',
        ]);

        $designation->update($request->only('nom', 'manche', 'prix_defaut'));

        return back()->with('success', 'Désignation mise à jour.');
    }

    public function destroy(Designation $designation)
    {
        if (Auth::user()->role !== 'directeur') {
            abort(403);
        }

        $designation->delete();
        return back()->with('success', 'Désignation supprimée du catalogue.');
    }
}