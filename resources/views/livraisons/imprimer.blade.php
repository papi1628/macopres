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
        .page{
            max-width:800px;
            width:100%;
            margin:auto;
            padding:30px 20px 60px;
            box-sizing:border-box;
            font-size:13px;
        }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
            min-width:100%;
        }
        
        .table-scroll{
            width:100%;
            overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }
        th, td { padding: 7px 10px; font-size: 12px; text-align: left; border: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; font-size: 10px; text-transform: uppercase; color: #64748b; }
        @media (max-width:640px){

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
                padding:20px 12px 35px;
            }

            h1{
                font-size:18px !important;
                text-align:center;
            }
            .signatures{
                grid-template-columns:1fr !important;
                gap:30px !important;
            }
            table{
                min-width:520px;
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
        <div class="table-scroll">
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
        </div>

        {{-- Client --}}
        <div class="table-scroll">
            <table>
            <thead><tr><th>Client</th><th>Contact</th></tr></thead>
            <tbody>
                <tr>
                    <td style="font-weight:700;">{{ strtoupper($ecole->nom) }}</td>
                    <td>{{ $ecole->contact_nom ?? '' }} {{ $ecole->contact_telephone ?? $ecole->telephone ?? '' }}</td>
                </tr>
            </tbody>
            </table>
        </div>

        {{-- Articles livrés --}}
        <div class="table-scroll">
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
        </div>

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
        <div class="signatures" style="display:grid; grid-template-columns:1fr 1fr; gap:40px; margin-top:60px;">
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