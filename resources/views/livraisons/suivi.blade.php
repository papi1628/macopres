<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de suivi — {{ $programme->ecole->nom }}</title>
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
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { padding: 6px 10px; font-size: 12px; text-align: center; border: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
        td:first-child, th:first-child { text-align: left; }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">Tableau de suivi des livraisons — {{ $programme->ecole->nom }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#166534,#3B6D11)">
            Imprimer
        </button>
    </div>

    <div class="page">
        <h1 style="text-align:center; font-size:16px; font-weight:800; letter-spacing:.02em; margin-bottom:4px;">
            TABLEAU DE SUIVI DES LIVRAISONS
        </h1>
        <p style="text-align:center; font-size:13px; font-weight:700; margin-bottom:20px;">{{ strtoupper($programme->ecole->nom) }}</p>

        @foreach($groupes as $groupe)
            <table>
                <thead>
                    <tr>
                        <th>Tailles</th>
                        <th colspan="1">{{ $groupe['libelle'] }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groupe['tailles'] as $taille => $qte)
                        <tr>
                            <td>{{ $taille ?: '–' }}</td>
                            <td style="{{ $qte['livre'] >= $qte['commande'] ? 'color:#3B6D11; font-weight:700;' : '' }}">
                                {{ $qte['livre'] }}/{{ $qte['commande'] }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background:#f8fafc; font-weight:800;">
                        <td>TOTAL</td>
                        <td>{{ $groupe['total_livre'] }}/{{ $groupe['total_commande'] }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach

        <p style="font-size:9px; color:#cbd5e1; margin-top:10px; text-align:center;">
            Généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
        </p>
    </div>
</body>
</html>