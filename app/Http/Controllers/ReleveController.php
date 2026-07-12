<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Programme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReleveController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FICHE DE GESTION DU RELEVÉ (ajout de versements)
    |--------------------------------------------------------------------------
    */
    public function show(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.facture', 'paiements.receveur');

        return view('releves.show', compact('programme'));
    }

    /*
    |--------------------------------------------------------------------------
    | ENREGISTRER UN VERSEMENT
    |--------------------------------------------------------------------------
    | Seul le montant est obligatoire. Le montant ne peut jamais dépasser
    | le reste à payer (le client ne peut pas verser plus que ce qu'il doit).
    */
    public function storePaiement(Request $request, Programme $programme)
    {
        $programme->load('bonsCommande.facture', 'paiements');
        $reste = $this->calculerTotaux($programme)['reste_a_payer'];

        $request->validate([
            'montant' => ['required', 'numeric', 'min:0.01', function ($attr, $value, $fail) use ($reste) {
                if ($value > $reste + 0.01) {
                    $fail('Ce montant dépasse le reste à payer (' . number_format($reste, 0, ',', ' ') . ' FCFA).');
                }
            }],
            'date'          => 'nullable|date',
            'mode_paiement' => 'nullable|in:cheque,virement,wave,orange_money,espece,agent_mandate',
            'reference'     => 'nullable|string|max:255',
        ]);

        $paiement = $programme->paiements()->create([
            'date'          => $request->date ?: now()->format('Y-m-d'),
            'montant'       => $request->montant,
            'mode_paiement' => $request->mode_paiement ?: 'espece',
            'reference'     => $request->reference,
            'recu_par'      => Auth::id(),
        ]);

        return response()->json([
            'success'  => true,
            'paiement' => $this->paiementAsArray($paiement),
            'totaux'   => $this->calculerTotaux($programme->fresh(['bonsCommande.facture', 'paiements'])),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MODIFIER UN VERSEMENT
    |--------------------------------------------------------------------------
    */
    public function updatePaiement(Request $request, Paiement $paiement)
    {
        $programme = $paiement->programme;
        $programme->load('bonsCommande.facture', 'paiements');

        // Reste à payer en excluant CE versement (puisqu'on va le remplacer)
        $totalFactures       = $programme->bonsCommande->sum(fn($b) => $b->facture->montant ?? 0);
        $totalVerseSansLigne = $programme->paiements->where('id', '!=', $paiement->id)->sum('montant');
        $reste               = $totalFactures - $totalVerseSansLigne;

        $request->validate([
            'montant' => ['required', 'numeric', 'min:0.01', function ($attr, $value, $fail) use ($reste) {
                if ($value > $reste + 0.01) {
                    $fail('Ce montant dépasse le reste à payer (' . number_format($reste, 0, ',', ' ') . ' FCFA).');
                }
            }],
            'date'          => 'required|date',
            'mode_paiement' => 'nullable|in:cheque,virement,wave,orange_money,espece,agent_mandate',
            'reference'     => 'nullable|string|max:255',
        ]);

        $paiement->update([
            'date'          => $request->date,
            'montant'       => $request->montant,
            'mode_paiement' => $request->mode_paiement ?: 'espece',
            'reference'     => $request->reference,
        ]);

        return response()->json([
            'success'  => true,
            'paiement' => $this->paiementAsArray($paiement->fresh()),
            'totaux'   => $this->calculerTotaux($programme->fresh(['bonsCommande.facture', 'paiements'])),
        ]);
    }

    public function destroyPaiement(Paiement $paiement)
    {
        $programme = $paiement->programme;
        $paiement->delete();

        return response()->json([
            'success' => true,
            'totaux'  => $this->calculerTotaux($programme->fresh(['bonsCommande.facture', 'paiements'])),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */
    private function calculerTotaux(Programme $programme): array
    {
        $totalFactures = $programme->bonsCommande->sum(fn($b) => $b->facture->montant ?? 0);
        $totalVerse    = $programme->paiements->sum('montant');
        $reste         = $totalFactures - $totalVerse;

        return [
            'total_factures' => (float) $totalFactures,
            'total_verse'    => (float) $totalVerse,
            'reste_a_payer'  => (float) $reste,
            'solde'          => $totalFactures > 0 && $reste <= 0.01,
        ];
    }

    private function paiementAsArray(Paiement $paiement): array
    {
        return [
            'id'            => $paiement->id,
            'date'          => $paiement->date->format('d/m/Y'),
            'date_iso'      => $paiement->date->format('Y-m-d'),
            'montant'       => (float) $paiement->montant,
            'mode_paiement' => $paiement->mode_paiement,
            'mode_label'    => $paiement->modeLabel(),
            'reference'     => $paiement->reference,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT IMPRIMABLE
    |--------------------------------------------------------------------------
    */
    public function imprimer(Programme $programme)
    {
        $programme->load('ecole', 'bonsCommande.facture', 'paiements');

        return view('releves.imprimer', compact('programme'));
    }
}