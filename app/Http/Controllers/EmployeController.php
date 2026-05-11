<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeController extends Controller
{
    public function index()
    {
        $employes = Employe::latest()->paginate(10);

        $departements = Employe::select('departement')
            ->distinct()
            ->pluck('departement');

        return view('employes.index', compact(
            'employes',
            'departements'
        ));
    }

    public function create()
    {
        return view('employes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'matricule'   => 'required|unique:employes',
            'nom'         => 'required',
            'prenom'      => 'required',
            'tel'         => 'required',
            'poste'       => 'required',
            'departement' => 'required',
        ]);

        Employe::create([
            'matricule'   => $request->matricule,
            'nom'         => $request->nom,
            'prenom'      => $request->prenom,
            'tel'         => $request->tel,
            'poste'       => $request->poste,
            'departement' => $request->departement,
            'date_embauche' => $request->date_embauche,
            'salaire'     => $request->salaire,
            'actif'       => $request->actif ?? true,
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('employes.index')
            ->with('success', 'Employé créé.');
    }

    public function update(Request $request, Employe $employe)
    {
        $request->validate([
            'nom'         => 'required',
            'prenom'      => 'required',
        ]);

        $employe->update([
            'nom'             => $request->nom,
            'prenom'          => $request->prenom,
            'tel'             => $request->tel,
            'poste'           => $request->poste,
            'departement'     => $request->departement,
            'date_embauche'   => $request->date_embauche,
            'salaire'         => $request->salaire,
            'actif'           => $request->actif,
        ]);

        return redirect()
            ->route('employes.index')
            ->with('success', 'Employé modifié.');
    }

    public function destroy(Employe $employe)
    {
        $employe->delete();

        return redirect()
            ->route('employes.index')
            ->with('success', 'Employé supprimé.');
    }

    public function qr(Employe $employe)
    {
        return view('employes.qr', compact('employe'));
    }
}