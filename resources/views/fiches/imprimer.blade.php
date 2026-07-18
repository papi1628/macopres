<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $titre }} — {{ $bonCommande->programme->ecole->nom }}</title>
    @vite(['resources/css/app.css'])

   <style>

body {
    font-family:'Inter', Arial, sans-serif;
    background:#f1f5f9;
    color:#1e293b;
}


/* BARRE OUTILS */

.barre-outils {
    position:sticky;
    top:0;
}


/* IMPRESSION */

@media print {

    .barre-outils {
        display:none !important;
    }

    body {
        background:white;
    }

    .page {
        margin:0;
        box-shadow:none;
    }

    .groupe {
        page-break-inside:avoid;
    }

}


/* PAGE A4 */

.page {
    width:210mm;
    max-width:100%;
    min-height:297mm;

    margin:20px auto;

    background:white;

    padding:20mm;

    box-sizing:border-box;
}



/* GROUPES DE PRODUCTION */

.groupe {

    margin-top:30px;

    padding-top:20px;

    border-top:2px solid #0C447C;

    page-break-after:always;

}


.groupe:last-child {

    page-break-after:auto;

}



/* TABLEAUX */

.table-container {

    width:100%;

    overflow-x:auto;

    -webkit-overflow-scrolling:touch;

}


table {

    width:100%;

    border-collapse:collapse;

}


th {

    background:#0C447C;

    color:white;

    padding:10px;

    font-size:12px;

    text-transform:uppercase;

    white-space:nowrap;

}


td {

    padding:9px;

    font-size:12px;

    border-bottom:1px solid #e2e8f0;

    text-align:center;

}



/* DESCRIPTION */

.description-box {

    margin-top:15px;

    margin-bottom:15px;

    padding:12px;

    background:#f8fafc;

    border-radius:8px;

    font-size:15px;

    font-weight: 600;

    text-align: center;

}



/* PHOTO */

.photo-box {

    height:280px;

    width:100%;

    border:1px solid #cbd5e1;

    border-radius:12px;

    display:flex;

    justify-content:center;

    align-items:center;

    overflow:hidden;

    margin-top:10px;

    background:#fff;

}


.photo-box img {

    width:100%;
    height:100%;

    max-width:100%;
    max-height:100%;

    object-fit:cover;

}



.photo-box span {

    color:#94a3b8;

    font-size:12px;

}

.grid-2 {

    display:grid;

    grid-template-columns:1fr 1fr;

    gap:15px;

}


@media(max-width:640px){

.grid-2 {

    grid-template-columns:1fr;

}

}



/* MOBILE */

@media(max-width:640px){


    body {

        background:white;

    }


    .barre-outils {

        padding:12px 15px !important;

        gap:10px;

        flex-wrap:wrap;

    }


    .barre-outils p {

        font-size:12px !important;

        max-width:100%;

        flex:1;

    }


    .barre-outils button {

        height:36px !important;

        padding:0 14px !important;

        font-size:12px !important;

        border-radius:10px;

    }



    .page {

        width:100%;

        min-height:auto;

        margin:0;

        padding:20px 12px 40px;

    }



    .groupe {

        margin-top:20px;

        padding-top:15px;

    }



    .photo-box {

        height:220px;

    }



    th {

        font-size:11px;

        padding:8px;

    }


    td {

        font-size:11px;

        padding:8px;

    }


}



/* PETITS TELEPHONES */

@media(max-width:380px){


    .barre-outils {

        flex-direction:column;

        align-items:stretch !important;

    }


    .barre-outils button {

        width:100%;

    }


    .page {

        padding:15px 10px 30px;

    }


    .photo-box {

        height:180px;

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
                <div style="
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                gap:15px;
                margin-bottom:12px;
                ">

                    <div>

                        <h3 style="
                        margin:0;
                        font-size:16px;
                        font-weight:900;
                        color:#0C447C;
                        text-transform:uppercase;
                        ">
                            {{ $groupe['libelle'] }}
                        </h3>


                        <div style="
                        margin-top:6px;
                        display:flex;
                        flex-wrap:wrap;
                        gap:6px;
                        ">

                            @if($groupe['couleur'])
                            <span style="
                            background:#E6F1FB;
                            color:#185FA5;
                            padding:4px 8px;
                            border-radius:999px;
                            font-size:10px;
                            font-weight:700;
                            ">
                                Couleur : {{ $groupe['couleur'] }}
                            </span>
                            @endif


                            @if($groupe['matiere'])
                            <span style="
                            background:#f1f5f9;
                            color:#475569;
                            padding:4px 8px;
                            border-radius:999px;
                            font-size:10px;
                            font-weight:700;
                            ">
                                Matière : {{ $groupe['matiere'] }}
                            </span>
                            @endif


                            {{--@if($groupe['logo'])
                            <span style="
                            background:#EAF3DE;
                            color:#3B6D11;
                            padding:4px 8px;
                            border-radius:999px;
                            font-size:10px;
                            font-weight:700;
                            ">
                                Avec logo
                            </span>
                            @endif --}}

                        </div>

                    </div>


                    {{-- <div style="
                    background:#0C447C;
                    color:white;
                    padding:6px 10px;
                    border-radius:8px;
                    font-size:11px;
                    font-weight:800;
                    white-space:nowrap;
                    ">
                        {{ $groupe['total'] }} pièces
                    </div> --}}

                </div>
                @if($groupe['note']?->description)

                    <div class="description-box">
                        {{ $groupe['note']->description }}
                    </div>

                @endif

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Tailles</th>
                                <th>Quantités</th>
                            </tr>
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
                    <div class="photo-box">

                        @if($groupe['note']?->photoUrl())

                            <img src="{{ $groupe['note']->photoUrl() }}">

                        @else

                            <span>
                                Aucune photo
                            </span>

                        @endif

                    </div>
                </div>
            </div>
        @endforeach

        <p style="font-size:9px; color:#cbd5e1; margin-top:20px; text-align:center;">
            Fiche générée le {{ now()->format('d/m/Y à H:i') }} — MACOPRES
        </p>
    </div>
</body>
</html>