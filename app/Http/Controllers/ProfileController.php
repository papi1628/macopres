<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AFFICHAGE DU PROFIL
    |--------------------------------------------------------------------------
    */
    public function edit()
    {
        $user    = Auth::user();
        $employe = $user->employe; // lié via employes.user_id (uniquement pour un assistant)

        return view('profile.edit', compact('user', 'employe'));
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFIER LE LOGIN
    |--------------------------------------------------------------------------
    */
    public function updateLogin(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'login' => 'required|string|max:255|unique:users,login,' . $user->id,
        ]);

        $user->update(['login' => $request->login]);

        return back()->with('success', 'Login mis à jour avec succès.');
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGER LE MOT DE PASSE
    |--------------------------------------------------------------------------
    */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])
                ->with('error', 'Le mot de passe actuel est incorrect.');
        }

        $user->update([
            'password' => $request->password, // auto-hashé grâce au cast 'hashed' du modèle
        ]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }
}