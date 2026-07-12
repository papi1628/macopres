<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titre }} — {{ $bonCommande->programme->ecole->nom }}</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .groupe { page-break-inside: avoid; }
        }
        .page { max-width: 800px; margin: 0 auto; padding: 30px 20px 60px; font-size: 13px; }
        .groupe { border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 10px; font-size: 12px; text-align: left; border: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">{{ $titre }} — {{ $bonCommande->programme->ecole->nom }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    @php $ecole = $bonCommande->programme->ecole; @endphp

    <div class="page">

        <h1 style="text-align:center; font-size:16px; font-weight:800; letter-spacing:.02em;">{{ $titre }}</h1>
        <p style="text-align:center; font-size:14px; font-weight:700; margin-top:4px;">{{ mb_strtoupper($ecole->nom) }}</p>
        <p style="text-align:center; font-size:12px; color:#64748b;">{{ $bonCommande->programme->annee_scolaire }}</p>

        <div style="display:flex; justify-content:space-between; margin:20px 0; font-size:12px;">
            <p>Date : {{ $bonCommande->date->format('d/m/Y') }}</p>
            <p style="font-weight:700;">{{ $numero }}</p>
        </div>

        @foreach($groupes as $groupe)
            <div class="groupe">
                <p style="font-weight:800; font-size:13px; margin-bottom:4px;">{{ mb_strtoupper($ecole->nom) }}</p>
                <p style="font-size:12px; margin-bottom:2px;">
                    <strong>{{ mb_strtoupper($groupe['libelle']) }}</strong>
                    @if($groupe['couleur']) — {{ $groupe['couleur'] }} @endif
                    @if($groupe['matiere']) , {{ $groupe['matiere'] }} @endif
                    @if($groupe['logo']) (avec logo) @endif
                </p>
                @if($groupe['note']?->description)
                    <p style="font-size:12px; color:#475569; white-space:pre-line; margin-top:4px;">{{ $groupe['note']->description }}</p>
                @endif

                <table>
                    <thead>
                        <tr><th>Tailles</th><th>Quantités</th></tr>
                    </thead>
                    <tbody>
                        @foreach($groupe['tailles'] as $taille => $quantite)
                            <tr><td>{{ $taille ?: '–' }}</td><td>{{ $quantite }}</td></tr>
                        @endforeach
                        <tr style="background:#f8fafc; font-weight:800;">
                            <td>TOTAL</td><td>{{ $groupe['total'] }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:14px;">
                    <p style="font-size:10px; text-transform:uppercase; color:#94a3b8; margin-bottom:6px;">Photo</p>
                    @if($groupe['note']?->photoUrl())
                        <img src="{{ $groupe['note']->photoUrl() }}" style="max-width:180px; max-height:180px; border-radius:8px; border:1px solid #e2e8f0;">
                    @else
                        <div style="width:180px; height:100px; border:1px dashed #cbd5e1; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#cbd5e1; font-size:10px;">
                            Aucune photo
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <p style="font-size:9px; color:#cbd5e1; margin-top:20px; text-align:center;">
            Fiche générée le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
        </p>
    </div>
</body>
</html>