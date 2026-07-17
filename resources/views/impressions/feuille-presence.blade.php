<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feuille de présence — MACOPRES</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .page { padding: 0 !important; }
            .jour-bloc { page-break-after: always; }
            .jour-bloc:last-child { page-break-after: auto; }
        }
        .page {
            max-width:900px;
            width:100%;
            margin:auto;
            padding:24px 20px 60px;
            box-sizing:border-box;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { padding: 8px 10px; font-size: 12px; text-align: left; }
        thead th {
            font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
        .jour-bloc { margin-bottom: 32px; }

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


            .jour-bloc{
                margin-bottom:25px;
            }


            .jour-bloc h2{
                font-size:13px !important;
                line-height:1.4;
            }


            .jour-bloc > div:first-child{
                flex-direction:column;
                gap:8px;
            }


            .jour-bloc span{
                font-size:10px !important;
            }


            table{
                min-width:700px;
            }


            th,
            td{
                padding:8px;
                font-size:11px;
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
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">
            Feuille de présence — {{ $debut->format('d/m/Y') }}{{ $debut->format('Y-m-d') !== $fin->format('Y-m-d') ? ' au ' . $fin->format('d/m/Y') : '' }}
        </p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    <div class="page">
        @foreach($feuilles as $feuille)
            <div class="jour-bloc">
                <div style="display:flex; align-items:baseline; justify-content:space-between; margin-bottom:10px;">
                    <h2 style="font-size:15px; font-weight:800; color:#0C447C; text-transform:capitalize;">
                        {{ $feuille['date']->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    </h2>
                    <span style="font-size:11px; color:#64748b;">
                        {{ $feuille['stats']['presents'] }} présent(s) ·
                        {{ $feuille['stats']['retards'] }} retard(s) ·
                        {{ $feuille['stats']['absents'] }} absent(s)
                        / {{ $feuille['stats']['total'] }}
                    </span>
                </div>

                <div class="table-scroll">

                    <table style="border:1px solid #e2e8f0; border-radius:10px; overflow:hidden;">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Employé</th>
                            <th>Département</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feuille['lignes'] as $ligne)
                            @php $emp = $ligne->employe; @endphp
                            <tr>
                                <td style="font-family:monospace; font-weight:700; color:#0C447C;">{{ $emp->matricule ?? '–' }}</td>
                                <td style="font-weight:600;">{{ $emp->prenom ?? '' }} {{ $emp->nom ?? '' }}</td>
                                <td style="color:#64748b;">{{ $emp->departement ?? '–' }}</td>
                                <td style="font-family:monospace;">
                                    {{ $ligne->heure_arrivee ? substr($ligne->heure_arrivee, 0, 5) : '–' }}
                                    @if($ligne->retard)
                                        <span class="badge" style="background:#FAEEDA; color:#854F0B;">+{{ $ligne->minutes_retard }}min</span>
                                    @endif
                                </td>
                                <td style="font-family:monospace;">{{ $ligne->heure_depart ? substr($ligne->heure_depart, 0, 5) : '–' }}</td>
                                <td>
                                    @php
                                        $styles = [
                                            'present'   => ['Présent', '#EAF3DE', '#3B6D11'],
                                            'retard'    => ['Retard', '#FAEEDA', '#854F0B'],
                                            'absent'    => ['Absent', '#FCEBEB', '#A32D2D'],
                                            'dimanche'  => ['Week-end', '#F1F5F9', '#64748B'],
                                            'ferie_paye'=> ['Férié payé', '#DBEAFE', '#1D4ED8'],
                                        ];
                                        [$label, $bg, $color] = $styles[$ligne->statut] ?? [$ligne->statut, '#F1F5F9', '#64748B'];
                                    @endphp
                                    <span class="badge" style="background:{{ $bg }}; color:{{ $color }};">{{ $label }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <p style="font-size:10px; color:#94a3b8; margin-top:12px;">Généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES RH</p>
    </div>
</body>
</html>