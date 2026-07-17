<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titre }} — {{ $bonCommande->programme->ecole->nom }}</title>
    @vite(['resources/css/app.css'])

   <style>
        body {
            font-family:'Inter',Arial,sans-serif;
            background:#f1f5f9;
            color: #1e293b;
        }

        .barre-outils {
            position: sticky;
            top: 0;
        }

        @media print {
            .barre-outils {
                display: none !important;
            }

            body {
                background: white;
            }

            .groupe {
                page-break-inside: avoid;
            }
        }


        .page{

            max-width:800px;
            width:100%;

            margin:auto;

            background:white;

            padding:35px 45px;

            min-height:1100px;

            box-sizing:border-box;

        }


        .groupe{

            margin-top:30px;

            padding-top:20px;

            border-top:2px solid #0C447C;

        }


        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }


        table {
            width:100%;
            border-collapse:collapse;
            min-width:520px;
        }


        th {
            background:#0C447C;
            color:white;
            padding:10px;
            font-size:14px;
            text-transform:uppercase;
            white-space:nowrap;
        }

        td {
            padding:8px;
            font-size:12px;
            border-bottom:1px solid #e2e8f0;
            text-align:center;
        }


        img {
            max-width: 100%;
            height: auto;
        }

        .groupe{

            page-break-after:always;

        }

        .groupe:last-child{

            page-break-after:auto;

        }

        


        /* MOBILE */
        @media (max-width: 640px) {

            body {
                background: white;
            }


            .barre-outils {
                padding: 12px 15px !important;
                gap: 10px;
                align-items: center;
            }


            .barre-outils p {
                font-size: 12px !important;
                line-height: 1.3;
                max-width: 65%;
            }


            .barre-outils button {
                height: 36px !important;
                padding: 0 14px !important;
                font-size: 12px !important;
                border-radius: 10px;
                white-space: nowrap;
            }



            .page {
                padding: 20px 12px 40px;
                font-size: 12px;
            }



            h1 {
                font-size: 15px !important;
            }


            .groupe {
                padding: 14px;
                margin-bottom: 15px;
                border-radius: 12px;
            }



            .groupe p {
                line-height: 1.4;
            }


            table {
                min-width: 280px;
            }


            th,
            td {
                padding: 8px;
                font-size: 14px;
            }


            .photo img {
                width: 100%;
                max-width: 180px;
            }

        }



        /* PETITS TELEPHONES */
        @media (max-width: 380px) {


            .barre-outils {
                flex-direction: column;
                align-items: stretch !important;
            }


            .barre-outils p {
                max-width: 100%;
            }


            .barre-outils button {
                width: 100%;
            }


            .page {
                padding: 15px 10px 30px;
            }


            .groupe {
                padding: 12px;
            }

        }

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

        <div style="
        text-align:center;
        border-bottom:3px solid #0C447C;
        padding-bottom:18px;
        margin-bottom:25px;
        ">

            <h1 style="
            font-size:30px;
            font-weight:900;
            color:#0C447C;
            margin:0;
            ">
                MACOPRES
            </h1>

            <p style="font-size:11px;margin-top:15px;">
                Siège Social : DAKAR (SENEGAL), 14 Cité Fadia
            </p>

            <p style="font-size:11px;">
                RCCM : SN.DKR.2017.B.12286
                &nbsp; | &nbsp;
                NINEA : 006363775-2T2
            </p>

            <p style="font-size:11px;">
                www.macopresgroup.sn
                -
                contact@macopres.sn
                -
                +221 33 855 16 70 / +221 77 659 42 18
            </p>

        </div>

        {{-- TITRE BC --}}


        <div style="
        display:flex;
        flex-wrap:wrap;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        background:#f8fafc;
        border-radius:14px;
        padding:18px 22px;
        margin-bottom:20px;
        ">

        <div>

        <h2 style="
        font-size:20px;
        font-weight:900;
        color:#0C447C;
        margin:0;
        ">

        {{ $titre }}

        </h2>

        </div>

        <div style="
        display:grid;
        gap:8px 20px;
        font-size:10px;
        ">

        <p>
        <span style="font-weight:700;color:#64748b">
        NUMERO :
        </span>
        <span style="font-weight:900;color:#0C447C">
        {{ $numero }}
        </span>
        </p>




        <p>
        <span style="font-weight:700;color:#64748b">
        DATE :
        </span>
        {{ $bonCommande->date->format('d/m/Y') }}
        </p>

        </div>

        </div>

        {{-- ECOLE ANNEE --}}


        <div class="grid-2" style="margin-bottom:20px;">

            <div style="
            background:#f8fafc;
            padding:12px 15px;
            border-radius:12px;
            ">

            <p style="
            font-size:10px;
            color:#94a3b8;
            text-transform:uppercase;
            text-decoration:underline;
            margin-bottom:5px;
            ">
            ECOLE
            </p>

            <p style="
            font-weight:900;
            font-size:14px;
            ">
            {{ mb_strtoupper($ecole->nom) }}
            </p>

            </div>



            <div style="
            background:#f8fafc;
            padding:12px 15px;
            border-radius:12px;
            ">

            <p style="
            font-size:10px;
            color:#94a3b8;
            text-transform:uppercase;
            text-decoration:underline;
            margin-bottom:5px;
            ">
            ANNÉE SCOLAIRE
            </p>

            <p style="
            font-weight:900;
            font-size:14px;
            ">
            {{ $bonCommande->programme->annee_scolaire }}
            </p>

            </div>

            </div>


        @foreach($groupes as $groupe)
            <div class="groupe">
                <p style="font-size:12px; margin-bottom:2px;">
                    <strong>{{ mb_strtoupper($groupe['libelle']) }}</strong>
                    
                    @if($groupe['couleur']) — {{ $groupe['couleur'] }} @endif
                    @if($groupe['matiere']) , {{ $groupe['matiere'] }} @endif
                    @if($groupe['logo']) (avec logo) @endif
                </p>
                @if($groupe['note']?->description)
                    <p style="font-size:12px; color:#475569; white-space:pre-line; margin-top:4px;">{{ $groupe['note']->description }}</p>
                @endif

                <div class="table-container">
                    <table>
                        <thead>
                            <th>Tailles</th><th>Quantités</th></tr>
                        </thead>
                        <tbody>
                            @foreach($groupe['tailles'] as $taille => $quantite)
                                <tr><td style="font-weight:600">{{ $taille ?: '–' }}</td><td style="font-weight:bold">{{ $quantite }}</td></tr>
                            @endforeach
                            <tr style="background:#f8fafc">

                                <td style="font-weight:800">

                                    TOTAL

                                </td>

                                <td style="
                                font-size:14px;
                                font-weight:900;
                                color:#0C447C;
                                ">

                                    {{ $groupe['total'] }}

                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="margin-top:14px;">
                    <p style="font-size:10px; text-transform:uppercase; color:#94a3b8; margin-bottom:6px;">Photo</p>
                    @if($groupe['note']?->photoUrl())
                    <div style="
                    border:1px solid #CBD5E1;
                    height:300px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    ">
                        <img src="{{ $groupe['note']->photoUrl() }}" style="max-width:250%; max-height:250px; border-radius:8px; border:1px solid #e2e8f0;">
                    </div>
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