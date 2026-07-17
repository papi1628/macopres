<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bulletin de salaire — MACOPRES</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; color: #1e293b; }
        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
            .bulletin { padding: 0 !important; box-shadow: none !important; border: none !important; }
            .bulletin-page { page-break-after: always; }
            .bulletin-page:last-child { page-break-after: auto; }
        }

        .bulletin-page { display: flex; justify-content: center; padding: 20px 0; }
        .bulletin {
            width: 100%;
            max-width: 760px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 32px 36px;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px 10px; font-size: 12px; text-align: left; }
        thead th {
            font-size: 10px; text-transform: uppercase; letter-spacing: .04em;
            color: #94a3b8; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
        }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
        /* RESPONSIVE MOBILE */

        .table-container {
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }


        .header-bulletin {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
        }


        .info-employe {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:16px;
        }


        .net-payer {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:15px;
        }


        .signature-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:40px;
        }



        @media(max-width:640px){

            body{
                background:white;
            }


            .barre-outils{
                padding:12px 15px !important;
            }


            .barre-outils p{
                font-size:12px !important;
                max-width:60%;
                line-height:1.3;
            }


            .barre-outils button{
                height:36px !important;
                padding:0 14px !important;
                font-size:12px !important;
            }



            .bulletin-page{
                padding:10px 0;
            }


            .bulletin{

                border-radius:12px;

                padding:20px 14px;

                border:none;

            }



            .header-bulletin{

                flex-direction:column;

                align-items:flex-start;

                text-align:left !important;

            }


            .header-bulletin div:last-child{

                text-align:left !important;

                width:100%;

            }



            .info-employe{

                grid-template-columns:1fr;

            }



            .net-payer{

                flex-direction:column;

                align-items:flex-start;

            }


            .net-payer p:last-child{

                font-size:22px !important;

            }



            table{

                min-width:600px;

            }



            .table-wrapper{

                overflow-x:auto;

            }



            .signature-grid{

                grid-template-columns:1fr;

                gap:35px;

            }



        }



        @media(max-width:380px){


            .bulletin{

                padding:15px 10px;

            }


            h1{

                font-size:18px !important;

            }


        }
    </style>
</head>
<body>

    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">
            Bulletin de salaire — {{ $titrePeriode }} — {{ $fiches->count() }} employé(s)
        </p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    @forelse($fiches as $fiche)
        @php
            $employe = $fiche['employe'];
            $stats   = $fiche['stats'];
            $joursPayes = $stats['jours_presents'] + $stats['jours_feries_payes'];
        @endphp
        <div class="bulletin-page">
            <div class="bulletin">

                {{-- En-tête entreprise --}}
                <div class="header-bulletin" style="
                border-bottom:2px solid #0C447C;
                padding-bottom:16px;
                margin-bottom:20px;
                ">
                    <div>
                        <p style="font-size:16px; font-weight:800; color:#0C447C; letter-spacing:.02em;">MACOPRES</p>
                        <p style="font-size:10px; color:#94a3b8; margin-top:2px;">Siège social : DAKAR (SENEGAL), 14 Cité Fadia</p>
                        <p style="font-size:10px; color:#94a3b8; margin-top:2px;">RCCM N° SN.DKR.2017.B.12286  NINEA : 006363775-2T2</p>
                        
                        <p style="font-size:11px; color:#64748b; margin-top:4px;">Tel : +221 33 855 16 70 / +221 77 659 42 18</p>
                        
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:13px; font-weight:800; color:#1e293b;">BULLETIN DE SALAIRE</p>
                        <p style="font-size:11px; color:#64748b; margin-top:2px;">{{ $titrePeriode }}</p>
                        <p style="font-size:10px; color:#94a3b8;">du {{ $debutPeriode->format('d/m/Y') }} au {{ $finPeriode->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Bloc employé --}}
                <div class="info-employe" style="margin-bottom:20px;">
                    <div style="background:#f8fafc; border-radius:12px; padding:12px 16px;">
                        <p style="font-size:9px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; margin-bottom:4px;">Employé</p>
                        <p style="font-size:14px; font-weight:800; color:#1e293b;">{{ $employe->prenom }} {{ $employe->nom }}</p>
                        <p style="font-size:11px; color:#64748b; margin-top:2px;">
                            {{ $employe->poste ?? '' }}{{ $employe->poste && $employe->departement ? ' · ' : '' }}{{ $employe->departement ?? '' }}
                        </p>
                    </div>
                    <div style="background:#f8fafc; border-radius:12px; padding:12px 16px;">
                        <p style="font-size:9px; text-transform:uppercase; letter-spacing:.04em; color:#94a3b8; margin-bottom:4px;">Matricule</p>
                        <p style="font-family:monospace; font-size:14px; font-weight:800; color:#0C447C;">{{ $employe->matricule }}</p>
                    </div>
                </div>

                {{-- Récapitulatif jours / salaire 
                <div class="table-wrapper">
                    <table>
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th style="text-align:center;">Jours</th>
                            <th style="text-align:right;">Montant (FCFA)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jours travaillés (présent / retard)</td>
                            <td style="text-align:center;">{{ $stats['jours_presents'] }}</td>
                            <td style="text-align:right;">–</td>
                        </tr>
                        <tr>
                            <td>Jours fériés payés</td>
                            <td style="text-align:center;">{{ $stats['jours_feries_payes'] }}</td>
                            <td style="text-align:right;">–</td>
                        </tr>
                        <tr>
                            <td style="color:#A32D2D;">Jours d'absence (non payés)</td>
                            <td style="text-align:center; color:#A32D2D;">{{ $stats['jours_absents'] }}</td>
                            <td style="text-align:right;">–</td>
                        </tr>
                        @if($stats['jours_retard'] > 0)
                            <tr>
                                <td style="color:#854F0B;">dont retards</td>
                                <td style="text-align:center; color:#854F0B;">{{ $stats['jours_retard'] }}</td>
                                <td style="text-align:right;">–</td>
                            </tr>
                        @endif
                        <tr style="background:#f8fafc; font-weight:800;">
                            <td>Total jours payés</td>
                            <td style="text-align:center;">{{ $joursPayes }}</td>
                            <td style="text-align:right;"></td>
                        </tr>
                    </tbody>
                    </table>
                </div> --}}

                {{-- Net à payer --}}
                <div class="net-payer" style="
                background:linear-gradient(135deg,#0C447C,#185FA5);
                border-radius:12px;
                padding:10px 20px;
                margin-bottom:24px;
                ">
                    <div>
                        <p style="font-size:10px; color:#B5D4F4; text-transform:uppercase; letter-spacing:.04em;">Net à payer</p>
                        @if($stats['salaire_mensuel'])
                            <p style="font-size:10px; color:#B5D4F4; margin-top:2px;">Base : {{ number_format($stats['salaire_mensuel'], 0, ',', ' ') }} FCFA / jour</p>
                        @endif
                    </div>
                    <p style="font-size:26px; font-weight:800; color:white;">{{ number_format($stats['salaire_periode'], 0, ',', ' ') }} F</p>
                </div>

                {{-- Détail journalier --}}
                <p style="font-size:11px; font-weight:700; color:#1e293b; margin-bottom:8px;">Détail des pointages</p>
                <div class="table-wrapper">
                    <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Arrivée</th>
                            <th>Départ</th>
                            <th style="text-align:right;">Salaire/j</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fiche['lignes'] as $ligne)
                            <tr>
                                <td style="font-family:monospace;">{{ $ligne->date ? \Carbon\Carbon::parse($ligne->date)->format('d/m/Y') : '–' }}</td>
                                <td style="font-family:monospace;">{{ $ligne->heure_arrivee ? substr($ligne->heure_arrivee, 0, 5) : '–' }}</td>
                                <td style="font-family:monospace;">{{ $ligne->heure_depart ? substr($ligne->heure_depart, 0, 5) : '–' }}</td>
                                <td style="text-align:right;">{{ $ligne->salaire_jour ? number_format($ligne->salaire_jour, 0, ',', ' ') . ' F' : '–' }}</td>
                                <td>
                                    @php $badge = $ligne->badge_statut ?? ['label' => $ligne->statut, 'bg' => '#F1F5F9', 'color' => '#64748B']; @endphp
                                    <span class="badge" style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }};">{{ $badge['label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">Aucun pointage pour cette période</td></tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>

                {{-- Signatures --}}
                <div class="signature-grid" style="margin-top:32px;">
                    <div style="text-align:center;">
                        <div style="border-top:1px solid #cbd5e1; padding-top:8px;">
                            <p style="font-size:10px; color:#94a3b8;">Signature de l'employé</p>
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <div style="border-top:1px solid #cbd5e1; padding-top:8px;">
                            <p style="font-size:10px; color:#94a3b8;">Signature du directeur</p>
                        </div>
                    </div>
                </div>

                <p style="font-size:9px; color:#cbd5e1; margin-top:30px; text-align:center;">
                    Bulletin généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
                </p>
            </div>
        </div>
    @empty
        <div class="bulletin-page">
            <p style="color:#94a3b8; padding:40px; text-align:center;">Aucun employé sélectionné.</p>
        </div>
    @endforelse

</body>
</html>