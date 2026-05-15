<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            'nom'         => 'required',
            'prenom'      => 'required',
            'tel'         => 'required',
            'departement' => 'required',
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric',
        ]);

        if (
            Auth::user()->role === 'assistant' &&
            strtolower($request->departement) === 'administration'
        ) {
            abort(403, 'Vous ne pouvez pas créer un assistant.');
        }

        /*
        |--------------------------------------------------------------------------
        | GÉNÉRATION MATRICULE
        |--------------------------------------------------------------------------
        */

        $prefixes = [
            'administration' => 'ADM',
            'salle de coupe'   => 'SDC',
            'salle de montage'       => 'SDM',
            'finition'             => 'FIN',
        ];

        $departement = strtolower($request->departement);

        $prefix = $prefixes[$departement] ?? 'EMP';

        $last = Employe::latest('id')->first();

        $count = $last ? $last->id + 1 : 1;

        $numero = str_pad($count, 4, '0', STR_PAD_LEFT);

        $annee = now()->format('y');

        $matricule = "{$prefix}-{$annee}-{$numero}";

        $token = bin2hex(random_bytes(16));


        $employe = Employe::create([
            'matricule'   => $matricule,
            'nom'         => $request->nom,
            'prenom'      => $request->prenom,
            'tel'         => $request->tel,
            'departement' => $request->departement,
            'qr_code' => $token,
            'date_embauche' => $request->date_embauche,
            'salaire'     => $request->salaire,
            'created_by'  => Auth::id(),
        ]);

        if ($employe->departement === 'administration') {

            $user = User::create([
                'login' => $employe->tel,

                'password' => Hash::make('pass'),

                'role' => 'assistant',

                'employe_id' => $employe->id,
            ]);

            $employe->update([
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->route('employes.index')
            ->with('success', 'Employé créé.');
    }

    public function update(Request $request, Employe $employe)
    {
        $request->validate([
            'nom'         => 'required',
            'prenom'      => 'required',
            'tel'         => 'required',
            'departement' => 'required',
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric',
        ]);

        if (
            Auth::user()->role === 'assistant' &&
            strtolower($request->departement) === 'administration'
        ) {
            abort(403, 'Vous ne pouvez pas créer un assistant.');
        }
        

        $employe->update([
            'nom'             => $request->nom,
            'prenom'          => $request->prenom,
            'tel'             => $request->tel,
            'departement'     => $request->departement,
            'date_embauche'   => $request->date_embauche,
            'salaire'         => $request->salaire,
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
        $data = json_encode([
            'id' => $employe->id,
            'matricule' => $employe->matricule,
            'token' => hash_hmac('sha256', $employe->qr_code, env('APP_KEY')),
            'timestamp' => now()->timestamp
        ]);
        $qr = base64_encode(
            QrCode::format('svg')
                ->size(250)
                ->generate($data)
        );

        return response()->json([
            'employe' => $employe->prenom . ' ' . $employe->nom,
            'matricule' => $employe->matricule,
            'qr' => $qr
        ]);
    }
}