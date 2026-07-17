<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Badges QR — MACOPRES</title>
    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; }

        .barre-outils { }
        @media print {
            .barre-outils { display: none !important; }
            body { background: white; }
        }

        /* ── Format "unique" : 1 badge par page ── */
        .page-unique {
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            page-break-after: always;
        }
        .page-unique:last-child { page-break-after: auto; }

        /* ── Format "planche" : grille de badges ── */
        .grille-planche {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8mm;
            padding: 10mm;
        }
        @media print {
            .grille-planche { padding: 5mm; }
        }

        .badge-carte {
            width: 90mm;
            height: 55mm;
            border-radius: 14px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 2px 10px rgba(12,68,124,.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background: white;
            break-inside: avoid;
        }

        .badge-carte.grand {
            width: 100mm;
            height: 150mm;
        }

        .badge-entete {
            background: linear-gradient(135deg, #0C447C, #185FA5);
            color: white;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .badge-corps {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 10px 12px;
            gap: 10px;
        }

        .badge-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* RESPONSIVE BADGES */

@media(max-width: 900px){

    .grille-planche{
        grid-template-columns:repeat(2, 1fr);
        padding:20px;
        gap:20px;
    }

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
        max-width:60%;
        line-height:1.3;
    }


    .barre-outils button{
        height:36px !important;
        padding:0 14px !important;
        font-size:12px !important;
        white-space:nowrap;
    }



    /* PLANche badges */

    .grille-planche{

        grid-template-columns:1fr;

        padding:15px;

        gap:15px;

    }



    .badge-carte{

        width:100%;

        max-width:340px;

        height:auto;

        min-height:120px;

        margin:auto;

    }



    .badge-entete{

        padding:8px 10px;

    }



    .badge-corps{

        padding:12px;

        gap:12px;

    }



    .badge-qr{

        width:70px !important;

        height:70px !important;

    }



    /* FORMAT UNIQUE */

    .page-unique{

        min-height:auto;

        padding:20px 10px;

    }


    .badge-carte.grand{

        width:100%;

        max-width:360px;

        height:auto;

        min-height:520px;

    }


}



@media(max-width:380px){


    .badge-corps{

        flex-direction:column;

        text-align:center;

    }


    .badge-carte.grand{

        min-height:450px;

    }


}
    </style>
</head>
<body>

    {{-- Barre d'action (masquée à l'impression) --}}
    <div class="barre-outils sticky top-0 z-10 bg-white border-b border-slate-200 px-5 py-3 flex items-center justify-between shadow-sm">
        <p class="text-sm font-semibold text-slate-700">{{ $employes->count() }} badge(s) — format {{ $format === 'unique' ? '1 par page' : 'planche' }}</p>
        <button onclick="window.print()"
                class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            Imprimer
        </button>
    </div>

    @if($format === 'unique')
        @foreach($employes as $employe)
            <div class="page-unique">
                <div class="badge-carte grand">
                    <div class="badge-entete">
                        <div>
                            <p style="font-size:13px; font-weight:800; letter-spacing:.04em;">MACOPRES</p>
                            <p style="font-size:9px; opacity:.85;">Badge employé</p>
                        </div>
                    </div>
                    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:20px; gap:14px;">
                        <div style="width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-weight:800; color:white; font-size:20px; background:linear-gradient(135deg,#185FA5,#378ADD);">
                            {{ mb_strtoupper(substr($employe->prenom,0,1).substr($employe->nom,0,1)) }}
                        </div>
                        <div style="text-align:center;">
                            <p style="font-size:18px; font-weight:800; color:#1e293b;">{{ $employe->prenom }} {{ $employe->nom }}</p>
                            <p style="font-size:12px; color:#64748b; margin-top:2px;">{{ $employe->poste ?? $employe->departement ?? '' }}</p>
                            <p style="font-family:monospace; font-size:12px; font-weight:700; color:#0C447C; margin-top:6px;">{{ $employe->matricule }}</p>
                        </div>
                        <div class="badge-qr" style="width:150px; height:150px;" data-employe-id="{{ $employe->id }}">
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">Chargement…</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="grille-planche">
            @foreach($employes as $employe)
                <div class="badge-carte">
                    <div class="badge-entete" style="padding:6px 10px;">
                        <p style="font-size:10px; font-weight:800; letter-spacing:.04em;">MACOPRES</p>
                    </div>
                    <div class="badge-corps">
                        <div class="badge-qr" style="width:64px; height:64px; flex-shrink:0;" data-employe-id="{{ $employe->id }}">
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:8px;">…</div>
                        </div>
                        <div style="min-width:0;">
                            <p style="font-size:12px; font-weight:800; color:#1e293b; line-height:1.2;">{{ $employe->prenom }} {{ $employe->nom }}</p>
                            <p style="font-size:9px; color:#64748b; margin-top:2px;">{{ $employe->poste ?? $employe->departement ?? '' }}</p>
                            <p style="font-family:monospace; font-size:10px; font-weight:700; color:#0C447C; margin-top:4px;">{{ $employe->matricule }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <script>
        // Charge le QR de chaque employé via l'endpoint existant /employes/{id}/qr
        document.querySelectorAll('.badge-qr').forEach(async (el) => {
            const id = el.dataset.employeId;
            try {
                const res = await fetch(`/employes/${id}/qr`);
                const data = await res.json();
                el.innerHTML = `<img src="data:image/svg+xml;base64,${data.qr}" alt="QR ${data.matricule}">`;
            } catch (e) {
                el.innerHTML = '<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:9px;">QR indisponible</div>';
            }
        });
    </script>
</body>
</html>