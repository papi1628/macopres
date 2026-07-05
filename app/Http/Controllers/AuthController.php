<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | AFFICHER LA PAGE LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | TRAITEMENT CONNEXION
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required'
        ]);


        if (Auth::attempt($credentials)) {
            
            Auth::user()->update(['last_login_at' => now()]);

            $request->session()->regenerate();

            if (auth()->user()->role === 'directeur') {
                return redirect('/dashboard/directeur');
            }

            return redirect('/dashboard/assistant');
        }

        return back()->withErrors([
            'login' => 'Identifiants incorrects',
        ]);
    }



    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}