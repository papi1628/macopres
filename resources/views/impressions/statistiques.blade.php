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
        .page {
            max-width:900px;
            width:100%;
            margin:auto;
            padding:24px 20px 60px;
            box-sizing:border-box;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; font-size: 12px; text-align: left; }
        thead th {
            font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        .kpi { border-left: 3px solid #0C447C; background: white; border-radius: 16px; padding: 16px; }
        .table-scroll {
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }
        @media(max-width:640px){

    body{
        background:white;
    }


    .barre-outils{
        padding:12px 15px !important;
        gap:10px;
    }


    .barre-outils p{
        font-size:12px !important;
        line-height:1.3;
        max-width:65%;
    }


    .barre-outils button{
        height:36px !important;
        padding:0 14px !important;
        font-size:12px !important;
        border-radius:10px;
    }


    .page{
        padding:20px 12px 40px;
    }


    /* En-tête */

    .page > div:first-child{
        flex-direction:column;
        align-items:flex-start !important;
        gap:8px;
    }


    h1{
        font-size:17px !important;
    }


    /* Cartes KPI */

    

    .kpi-grid{
        grid-template-columns:repeat(2, minmax(0,1fr)) !important;
        gap:10px !important;
    }


    .kpi{
        padding:14px;
    }


    .kpi p:last-child{
        font-size:20px !important;
    }


    /* Masse salariale */

    .kpi[style*="display:flex"]{
        flex-direction:column;
        align-items:flex-start !important;
        gap:8px;
    }


    /* Tableau */

    table{
        min-width:850px;
    }


    th,
    td{
        font-size:11px;
        padding:8px;
    }


}

@media(max-width:380px){

    .barre-outils{
        flex-direction:column;
        align-items:stretch !important;
    }


    .barre-outils p{
        max-width:100%;
    }


    .barre-outils button{
        width:100%;
    }


    .page{
        padding:15px 10px 30px;
    }

}

@media(max-width:640px){

    .kpi-grid{
        grid-template-columns:repeat(2, minmax(0,1fr)) !important;
        gap:10px !important;
    }

    .kpi{
        padding:12px;
    }

    .kpi .text-\[28px\]{
        font-size:22px !important;
    }

}
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
        <div class="kpi-grid grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"
                style="border-left:3px solid #0C447C">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                    Employés
                </p>
                <p class="text-[28px] font-black leading-none" style="color:#0C447C">
                    {{ $globalStats['nb_employes'] }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    employés actifs
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"
                style="border-left:3px solid #3B6D11">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                    Présences
                </p>
                <p class="text-[28px] font-black leading-none" style="color:#3B6D11">
                    {{ $globalStats['jours_presents'] }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    journées présentes
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"
                style="border-left:3px solid #A32D2D">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                    Absences
                </p>
                <p class="text-[28px] font-black leading-none" style="color:#A32D2D">
                    {{ $globalStats['jours_absents'] }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    journées perdues
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"
                style="border-left:3px solid #854F0B">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                    Retards
                </p>
                <p class="text-[28px] font-black leading-none" style="color:#854F0B">
                    {{ $globalStats['jours_retard'] }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    arrivées tardives
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5"
                style="border-left:3px solid #185FA5">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                    Masse salariale
                </p>
                <p class="text-[22px] font-black leading-none" style="color:#185FA5">
                    {{ number_format($globalStats['masse_salariale'],0,',',' ') }}
                </p>
                <p class="text-[10px] text-slate-400 mt-1">
                    FCFA période
                </p>
            </div>
        </div>

        

        {{-- Détail par département --}}
        <h2 style="font-size:14px; font-weight:700; margin-bottom:10px; color:#1e293b;">Détail par département</h2>
        <div class="table-scroll">

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
        </div>

        <p style="font-size:10px; color:#94a3b8; margin-top:20px;">Généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES RH</p>
    </div>
</body>
</html>