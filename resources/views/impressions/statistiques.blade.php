<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapport statistique — MACOPRES</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .page { padding: 0 !important; }
        }
        .page { max-width: 900px; margin: 0 auto; padding: 24px 20px 60px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; font-size: 12px; text-align: left; }
        thead th {
            font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        .kpi { border-left: 3px solid #0C447C; background: white; border-radius: 16px; padding: 16px; }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">Rapport statistique — {{ $titrePeriode }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    <div class="page">

        {{-- En-tête --}}
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
            <div>
                <h1 style="font-size:20px; font-weight:800; color:#0C447C;">MACOPRES — Rapport statistique</h1>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">{{ $titrePeriode }} · du {{ $debutPeriode->format('d/m/Y') }} au {{ $finPeriode->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- KPI globaux --}}
        <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:24px;">
            <div class="kpi">
                <p style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em;">Employés</p>
                <p style="font-size:22px; font-weight:800; color:#0C447C;">{{ $globalStats['nb_employes'] }}</p>
            </div>
            <div class="kpi" style="border-left-color:#3B6D11">
                <p style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em;">Présents</p>
                <p style="font-size:22px; font-weight:800; color:#3B6D11;">{{ $globalStats['jours_presents'] }}</p>
            </div>
            <div class="kpi" style="border-left-color:#A32D2D">
                <p style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em;">Absents</p>
                <p style="font-size:22px; font-weight:800; color:#A32D2D;">{{ $globalStats['jours_absents'] }}</p>
            </div>
            <div class="kpi" style="border-left-color:#854F0B">
                <p style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em;">Retards</p>
                <p style="font-size:22px; font-weight:800; color:#854F0B;">{{ $globalStats['jours_retard'] }}</p>
            </div>
            <div class="kpi" style="border-left-color:#185FA5">
                <p style="font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em;">Taux présence</p>
                <p style="font-size:22px; font-weight:800; color:#185FA5;">{{ $globalStats['taux_presence'] }}%</p>
            </div>
        </div>

        <div class="kpi" style="margin-bottom:28px; display:flex; align-items:center; justify-content:space-between;">
            <span style="font-size:12px; color:#64748b; font-weight:600;">Masse salariale de la période</span>
            <span style="font-size:20px; font-weight:800; color:#0C447C;">{{ number_format($globalStats['masse_salariale'], 0, ',', ' ') }} FCFA</span>
        </div>

        {{-- Détail par département --}}
        <h2 style="font-size:14px; font-weight:700; margin-bottom:10px; color:#1e293b;">Détail par département</h2>
        <table style="border:1px solid #e2e8f0; border-radius:12px; overflow:hidden;">
            <thead>
                <tr>
                    <th>Département</th>
                    <th style="text-align:center;">Employés</th>
                    <th style="text-align:center;">Présents</th>
                    <th style="text-align:center;">Absents</th>
                    <th style="text-align:center;">Fériés</th>
                    <th style="text-align:center;">Retards</th>
                    <th style="text-align:center;">Taux</th>
                    <th style="text-align:right;">Masse salariale</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statsParDepartement as $dept)
                    <tr>
                        <td style="font-weight:700; text-transform:capitalize;">{{ $dept['departement'] }}</td>
                        <td style="text-align:center;">{{ $dept['nb_employes'] }}</td>
                        <td style="text-align:center; color:#3B6D11; font-weight:700;">{{ $dept['jours_presents'] }}</td>
                        <td style="text-align:center; color:#A32D2D; font-weight:700;">{{ $dept['jours_absents'] }}</td>
                        <td style="text-align:center; color:#1D4ED8; font-weight:700;">{{ $dept['jours_feries'] }}</td>
                        <td style="text-align:center; color:#854F0B; font-weight:700;">{{ $dept['jours_retard'] }}</td>
                        <td style="text-align:center; font-weight:700;">{{ $dept['taux_presence'] }}%</td>
                        <td style="text-align:right; font-weight:700;">{{ number_format($dept['masse_salariale'], 0, ',', ' ') }} F</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:24px;">Aucune donnée pour cette période</td></tr>
                @endforelse
            </tbody>
        </table>

        <p style="font-size:10px; color:#94a3b8; margin-top:20px;">Généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES RH</p>
    </div>
</body>
</html>