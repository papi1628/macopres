<!DOCTYPE html>
<html lang="fr">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Relevé de compte — {{ $programme->ecole->nom }}</title>

@vite(['resources/css/app.css'])

<style>

body {
    font-family:'Inter',Arial,sans-serif;
    background:#f1f5f9;
    color:#1e293b;
}


.page {
    max-width:800px;
    width:100%;
    margin:auto;
    background:white;
    padding:35px 45px;
    min-height:1100px;
    box-sizing:border-box;
}

.toolbar { flex-wrap:wrap; gap:8px; }

.grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.signature-grid { display:grid; grid-template-columns:1fr 1fr; gap:60px; }

.table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }

table {
    width:100%;
    border-collapse:collapse;
    min-width:560px;
}


th {
    background:#0C447C;
    color:white;
    padding:10px;
    font-size:11px;
    text-transform:uppercase;
    white-space:nowrap;
}


td {
    padding:8px;
    font-size:12px;
    border-bottom:1px solid #e2e8f0;
}

@media (max-width: 640px) {
    .page { padding: 20px 16px; min-height:0; }
    .grid-2 { grid-template-columns:1fr; gap:12px; }
    .signature-grid { grid-template-columns:1fr; gap:30px; margin-top:50px !important; }
    h1 { font-size:22px !important; }
    h2 { font-size:16px !important; }
}

@media print {

    body {
        background:white;
    }

    .toolbar {
        display:none;
    }

    .page {
        padding:0;
        width:auto;
        max-width:none;
    }

    .table-scroll { overflow:visible; }

}

</style>

</head>


<body>


<div class="toolbar bg-white border-b px-5 py-3 flex justify-between items-center">

<p class="font-bold text-slate-700 text-sm">
Relevé de compte — {{ $programme->ecole->nom }}
</p>


<button onclick="window.print()"
class="px-5 py-2 rounded-xl text-white font-bold text-sm"
style="background:#185FA5">

Imprimer

</button>

</div>



@php

$ecole = $programme->ecole;


$factures = $programme->bonsCommande
    ->filter(fn($b)=>$b->facture)
    ->map(fn($b)=>$b->facture)
    ->sortBy('date')
    ->values();


$versements = $programme->paiements
    ->sortBy('date')
    ->values();



$totalFactures = $factures->sum('montant');
$totalVersements = $versements->sum('montant');
$resteAPayer = $totalFactures - $totalVersements;


$nbLignes = max($factures->count(),$versements->count(),1);

$dateReleve = $versements->last()?->date ?? now();

@endphp




<div class="page">



{{-- ENTETE --}}

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


<p style="font-size:11px;margin-top:15px">
Siège Social : DAKAR (SENEGAL), 14 Cité Fadia
</p>


<p style="font-size:11px">
RCCM : SN.DKR.2017.B.12286
&nbsp; | &nbsp;
NINEA : 006363775-2T2
</p>


<p style="font-size:11px">
www.macopresgroup.sn
-
contact@macopres.sn
-
+221 33 855 16 70 / +221 77 659 42 18
</p>


</div>






{{-- TITRE --}}


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

RELEVÉ DE COMPTE

</h2>

</div>



<div style="font-size:10px;">

<p>
<span style="font-weight:700;color:#64748b">
DATE :
</span>

{{ $dateReleve->format('d/m/Y') }}

</p>




</div>


</div>







{{-- CLIENT --}}


<div class="grid-2" style="margin-bottom:20px;">


<div style="
background:#f8fafc;
padding:15px;
border-radius:12px;
">


<p style="
font-size:10px;
color:#94a3b8;
text-transform:uppercase; text-decoration:underline
">

CLIENT

</p>


<p style="
font-weight:900;
font-size:15px;
">

{{ mb_strtoupper($ecole->nom) }}

</p>

</div>




<div style="
background:#f8fafc;
padding:15px;
border-radius:12px;
">


<p style="
font-size:10px;
color:#94a3b8;
text-transform:uppercase; text-decoration:underline
">
CONTACT
</p>


<p style="
font-weight:900;
font-size:15px;
">

{{ $ecole->contact_nom }}


<p style="font-size:12px;color:#64748b">

{{  $ecole->contact_telephone ?? $ecole->telephone ?? '' }}

</p>

</p>


</div>


</div>







{{-- TABLEAU --}}

<div class="table-scroll">

<table>


<thead>

<tr>

<th>Factures</th>

<th>Date</th>

<th style="text-align:right">
Montant facture
</th>


<th>Date versement</th>


<th style="text-align:right">
Montant versé
</th>


</tr>

</thead>




<tbody>


@for($i=0;$i<$nbLignes;$i++)


@php

$facture=$factures->get($i);
$versement=$versements->get($i);

@endphp



<tr>


<td style="font-weight:700">

{{ $facture?->numero }}

</td>


<td>

{{ $facture?->date?->format('d/m/Y') }}

</td>



<td style="text-align:right;font-weight:700;color:#185FA5">

@if($facture)
{{ number_format($facture->montant,0,',',' ') }} FCFA
@endif

</td>



<td  style="text-align:center">

{{ $versement?->date?->format('d/m/Y') }}

</td>


<td style="text-align:right;font-weight:700">

@if($versement)

{{ number_format($versement->montant,0,',',' ') }} FCFA

@endif

</td>



</tr>


@endfor



</tbody>




<tfoot>


<tr style="background:#f8fafc">


<td colspan="2"
style="font-weight:800">

TOTAL FACTURES

</td>


<td style="
text-align:right;
font-weight:900;
color:#0C447C;
">

{{ number_format($totalFactures,0,',',' ') }} FCFA

</td>



<td style="font-weight:800">

TOTAL VERSEMENTS

</td>


<td style="
text-align:right;
font-weight:900;
">

{{ number_format($totalVersements,0,',',' ') }} FCFA

</td>


</tr>


</tfoot>


</table>

</div>







{{-- RESTE --}}


<div style="
margin-top:25px;
background:#f8fafc;
border-radius:12px;
padding:15px;
text-align:center;
font-weight:900;
font-size:14px;
">




@if($resteAPayer <= 0.01 && $totalFactures > 0)

<span style="color:#3B6D11">
SOLDÉ
</span>


@else

<span style="color:#A32D2D">

RESTE À PAYER : {{ number_format($resteAPayer,0,',',' ') }} FCFA

</span>


@endif


</div>






@if($resteAPayer > 0.01)

<p style="
margin-top:20px;
font-size:12px;
font-style:italic;
">


Arrêté le présent relevé de compte à la somme de :


<strong>

{{ mb_strtoupper(\App\Support\NombreEnLettres::enMontant($resteAPayer)) }}

</strong>


</p>


@endif






{{-- SIGNATURE --}}


<div class="signature-grid" style="margin-top:80px;">


<div></div>



<div style="text-align:center">


<p style="
border-top:1px solid #cbd5e1;
padding-top:10px;
font-size:11px;
">

LA COMPTABILITÉ

</p>


</div>


</div>






<p style="
font-size:9px;
color:#cbd5e1;
margin-top:40px;
text-align:center;
">

Relevé généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES

</p>



</div>



</body>

</html>