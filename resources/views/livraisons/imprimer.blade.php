<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bordereau {{ $livraison->numero }} — {{ $livraison->programme->ecole->nom }}</title>
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
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 7px 10px; font-size: 12px; text-align: left; border: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
    </style>
</head>
<body>

    @php $ecole = $livraison->programme->ecole; @endphp

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">Bordereau {{ $livraison->numero }} — {{ $ecole->nom }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#166534,#3B6D11)">
            Imprimer
        </button>
    </div>

    <div class="page">

        <h1 style="text-align:center; font-size:16px; font-weight:800; letter-spacing:.02em; margin-bottom:16px;">BORDEREAU DE LIVRAISON</h1>

        {{-- En-tête bordereau --}}
        <table>
            <thead>
                <tr><th>Numéro</th><th>Date</th><th>Référence</th><th>Livreur</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight:700;">{{ $livraison->numero }}</td>
                    <td>{{ $livraison->date->format('d/m/Y') }}</td>
                    <td>{{ $livraison->reference }}</td>
                    <td>{{ $livraison->livreur ?? '–' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Client --}}
        <table>
            <thead><tr><th>Client</th><th>Contact</th></tr></thead>
            <tbody>
                <tr>
                    <td style="font-weight:700;">{{ strtoupper($ecole->nom) }}</td>
                    <td>{{ $ecole->contact_nom ?? '' }} {{ $ecole->contact_telephone ?? $ecole->telephone ?? '' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Articles livrés --}}
        <table>
            <thead>
                <tr><th>Désignations</th><th style="text-align:center;">Quantités</th></tr>
            </thead>
            <tbody>
                @foreach($livraison->lignes as $ligne)
                    <tr>
                        <td>{{ $ligne->ligneBonCommande->libelle() }} {{ $ligne->ligneBonCommande->taille }}</td>
                        <td style="text-align:center; font-weight:600;">{{ $ligne->quantite_livree }}</td>
                    </tr>
                @endforeach
                <tr style="background:#f8fafc; font-weight:800;">
                    <td>TOTAL</td>
                    <td style="text-align:center;">{{ $livraison->quantite }} pièces</td>
                </tr>
            </tbody>
        </table>

        {{-- Observation --}}
        <p style="margin-top:16px; font-size:12px;">
            @if($totalLivreCumule >= $totalCommande)
                La totalité de la commande a été livrée le {{ $livraison->date->format('d F Y') }}.
            @else
                {{ $totalLivreCumule }}/{{ $totalCommande }} pièces livrées à ce jour
                ({{ $totalCommande > 0 ? round(($totalLivreCumule / $totalCommande) * 100) : 0 }}%).
            @endif
        </p>

        {{-- Signatures --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-top:60px;">
            <div>
                <p style="font-size:11px;">Livreur :</p>
                <div style="border-top:1px solid #cbd5e1; margin-top:40px;"></div>
            </div>
            <div>
                <p style="font-size:11px;">Réceptionniste :</p>
                <div style="border-top:1px solid #cbd5e1; margin-top:40px;"></div>
            </div>
        </div>

        <p style="font-size:9px; color:#cbd5e1; margin-top:30px; text-align:center;">
            Bordereau généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
        </p>
    </div>
</body>
</html>