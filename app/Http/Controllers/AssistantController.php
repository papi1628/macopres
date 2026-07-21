<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employe;


class AssistantController extends Controller
{

    private function checkPermission()
    {
        if (!Auth::check()) {
            abort(403);
        }
        if (Auth::user()->role === 'assistant') {
            abort(403, 'Accès refusé');
        }
    }
    // LISTE
    public function index()
    {
        $this->checkPermission();

        $assistants = User::where('role', 'assistant')
            ->with('employe')
            ->latest()
            ->paginate(10);

        return view('assistants.index', compact('assistants'));
    }

    // FORMULAIRE CREATE
    public function create()
    {
        $this->checkPermission();

        return view('assistants.create');
    }

    // ENREGISTRER
    public function store(Request $request)
    {
        $this->checkPermission();

        $request->validate([
            'login' => 'required|unique:users',
            
            
            'nom'         => 'required',
            'prenom'      => 'required',
            'tel'         => 'required',
            
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric',
        ]);

        $user = User::create([
            'login' => $request->login,
            'password' => Hash::make('pass'),
            'role' => 'assistant',
        ]);

        $last = Employe::latest('id')->first();

        $count = $last ? $last->id + 1 : 1;

        $numero = str_pad($count, 4, '0', STR_PAD_LEFT);

        $annee = now()->format('y');

        $matricule = "ADM-{$annee}-{$numero}";

        $token = bin2hex(random_bytes(16));

        $employe = Employe::create([
            'user_id'     => $user->id,
            'matricule'   => $matricule,
            'nom'         => $request->nom,
            'prenom'      => $request->prenom,
            'tel'         => $request->tel,
            'departement' => 'administration',
            'qr_code' => $token,
            'date_embauche' => $request->date_embauche,
            'salaire'     => $request->salaire,
            'created_by'  => Auth::id(),
        ]);

        $user->update([
            'employe_id' => $employe->id,
        ]);

        $user->load('employe');

        return response()->json([
            'success' => true,
            'message' => 'Assistant créé.',
            'assistant' => $user,
        ]);
    }

    // FORMULAIRE EDIT
    public function edit($id)
    {
        $this->checkPermission();

        $assistant = User::findOrFail($id);

        return view('assistants.edit', compact('assistant'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $this->checkPermission();

        $assistant = User::findOrFail($id);


        $request->validate([
            'nom'         => 'required',
            'prenom'      => 'required',
            'tel'         => 'required',
            'date_embauche' => 'nullable|date',
            'salaire' => 'nullable|numeric',
            'login' => 'required|unique:users,login,' . $assistant->id,
        ]);

        $assistant->update([
            'login' => $request->login,
        ]);

        $assistant->employe -> update([
            'nom'             => $request->nom,
            'prenom'          => $request->prenom,
            'tel'             => $request->tel,
            'date_embauche'   => $request->date_embauche,
            'salaire'         => $request->salaire,
        ]);

        return response()->json([
            'success' => true,
            'assistant' => $assistant->load('employe')
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        $this->checkPermission();

        $assistant = User::findOrFail($id);

        $assistant->delete();


        return response()->json([
            'success' => true
        ]);
    }
}