<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AssistantController extends Controller
{
    // LISTE
    public function index()
    {
        $assistants = User::where('role', 'assistant')->get();

        return view('assistants.index', compact('assistants'));
    }

    // FORMULAIRE CREATE
    public function create()
    {
        return view('assistants.create');
    }

    // ENREGISTRER
    public function store(Request $request)
    {
        $request->validate([
            'login' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role' => 'assistant',
        ]);

        return redirect('/assistants')
            ->with('success', 'Assistant créé');
    }

    // FORMULAIRE EDIT
    public function edit($id)
    {
        $assistant = User::findOrFail($id);

        return view('assistants.edit', compact('assistant'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $assistant = User::findOrFail($id);

        $assistant->update([
            'login' => $request->login,
        ]);

        return redirect('/assistants')
            ->with('success', 'Assistant modifié');
    }

    // DELETE
    public function destroy($id)
    {
        $assistant = User::findOrFail($id);

        $assistant->delete();

        return back()->with('success', 'Assistant supprimé');
    }
}