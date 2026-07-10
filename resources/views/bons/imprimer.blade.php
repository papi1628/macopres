<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bonCommande->numero }} — {{ $bonCommande->programme->ecole->nom }}</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .page { padding: 0 !important; }
        }
        .page { max-width: 800px; margin: 0 auto; padding: 30px 20px 60px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { padding: 8px 10px; font-size: 12px; text-align: left; }
        thead th {
            font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">Bon de commande {{ $bonCommande->numero }} — {{ $bonCommande->programme->ecole->nom }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    @php
        $ecole = $bonCommande->programme->ecole;
    @endphp

    <div class="page">

        {{-- En-tête --}}
        <div style="display:flex; align-items:flex-start; justify-content:space-between; border-bottom:2px solid #0C447C; padding-bottom:16px; margin-bottom:20px;">
            <div>
                <p style="font-size:16px; font-weight:800; color:#0C447C; letter-spacing:.02em;">MACOPRES</p>
                <p style="font-size:10px; color:#94a3b8; margin-top:2px;">Confection, communication et prestation de services</p>
                <p style="font-size:10px; color:#94a3b8;">Contact : 77 659 42 18</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:14px; font-weight:800;">BON DE COMMANDE</p>
                <p style="font-family:monospace; font-size:15px; font-weight:800; color:#0C447C; margin-top:2px;">{{ $bonCommande->numero }}</p>
                <p style="font-size:11px; color:#64748b; margin-top:2px;">{{ $bonCommande->date->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Client --}}
        <div style="background:#f8fafc; border-radius:12px; padding:14px 18px; margin-bottom:16px;">
            <p style="font-size:9px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; margin-bottom:4px;">Client</p>
            <p style="font-size:14px; font-weight:800;">{{ mb_strtoupper($ecole->nom) }}</p>
            <p style="font-size:11px; color:#64748b; margin-top:2px;">
                {{ $ecole->adresse ?? '' }}
                {{ $ecole->adresse && ($ecole->contact_telephone || $ecole->telephone) ? ' · ' : '' }}
                {{ $ecole->contact_telephone ?? $ecole->telephone ?? '' }}
            </p>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:6px;">
            <div>
                <p style="font-size:9px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8;">Nature</p>
                <p style="font-size:12px; font-weight:600;">{{ $bonCommande->nature ?? '–' }}</p>
            </div>
            <div>
                <p style="font-size:9px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8;">Condition de paiement</p>
                <p style="font-size:12px; font-weight:600;">{{ $bonCommande->condition_paiement ?? '–' }}</p>
            </div>
        </div>

        {{-- Articles --}}
        <table style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Taille</th>
                    <th>Couleur</th>
                    <th>Matière</th>
                    <th style="text-align:center;">Logo</th>
                    <th style="text-align:center;">Qté</th>
                    <th style="text-align:right;">P.U.</th>
                    <th style="text-align:right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bonCommande->lignes as $ligne)
                    <tr>
                        <td style="font-weight:600;">{{ $ligne->libelle() }}</td>
                        <td>{{ $ligne->taille ?? '–' }}</td>
                        <td>{{ $ligne->couleur ?? '–' }}</td>
                        <td>{{ $ligne->matiere ?? '–' }}</td>
                        <td style="text-align:center;">{{ $ligne->logo ? '✓' : '–' }}</td>
                        <td style="text-align:center; font-weight:600;">{{ $ligne->quantite }}</td>
                        <td style="text-align:right;">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</td>
                        <td style="text-align:right; font-weight:700; color:#185FA5;">{{ number_format($ligne->montant_ligne, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:20px;">Aucun article</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f8fafc;">
                    <td colspan="7" style="text-align:right; font-weight:800; font-size:12px;">TOTAL</td>
                    <td style="text-align:right; font-weight:800; font-size:13px; color:#0C447C;">{{ number_format($bonCommande->montant, 0, ',', ' ') }} F</td>
                </tr>
            </tfoot>
        </table>

        @if($bonCommande->montant > 0)
            <p style="font-size:11px; font-style:italic; color:#64748b; margin-top:8px;">
                Arrêté le présent bon de commande à la somme de {{ \App\Support\NombreEnLettres::enMontant($bonCommande->montant) }}.
            </p>
        @endif

        {{-- Signatures --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-top:50px;">
            <div style="text-align:center;">
                <div style="border-top:1px solid #cbd5e1; padding-top:8px;">
                    <p style="font-size:10px; color:#94a3b8;">Signature du client</p>
                </div>
            </div>
            <div style="text-align:center;">
                <div style="border-top:1px solid #cbd5e1; padding-top:8px;">
                    <p style="font-size:10px; color:#94a3b8;">Signature et cachet MACOPRES</p>
                </div>
            </div>
        </div>

        <p style="font-size:9px; color:#cbd5e1; margin-top:30px; text-align:center;">
            Généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
        </p>
    </div>
</body>
</html>