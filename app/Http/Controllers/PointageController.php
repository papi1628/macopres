<?php

namespace App\Http\Controllers;

use App\Models\Pointage;

class PointageController extends Controller
{
    public function index()
    {
        $pointages = Pointage::with('employe')
            ->latest()
            ->get();

        return view('pointages.index', compact('pointages'));
    }

    public function scan()
    {
        return view('pointages.scan');
    }

    public function historique()
    {
        $pointages = Pointage::with('employe')
            ->latest()
            ->paginate(20);

        return view('pointages.historique', compact('pointages'));
    }
}