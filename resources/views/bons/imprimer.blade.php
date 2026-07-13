<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Bon de commande {{ $bonCommande->numero }}</title>

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
    min-width:520px;
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
    body { background:white; }
    .toolbar { display:none; }
    .page { padding:0; width:auto; max-width:none; }
    .table-scroll { overflow:visible; }
}

</style>

</head>


<body>


<div class="toolbar bg-white border-b px-5 py-3 flex justify-between items-center">

<p class="font-bold text-slate-700 text-sm">
Bon de commande {{ $bonCommande->numero }}
</p>


<button onclick="window.print()"
class="px-5 py-2 rounded-xl text-white font-bold text-sm"
style="background:#185FA5">

Imprimer

</button>


</div>



@php
$ecole = $bonCommande->programme->ecole;
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

BON DE COMMANDE

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
{{ $bonCommande->numero }}
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






{{-- CLIENT BENEFICIAIRE --}}


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

BENEFICIAIRE

</p>


<p style="
font-weight:900;
font-size:15px;
">

MACOPRES

</p>





</div>


</div>






{{-- INFORMATIONS --}}


<div style="margin-bottom:20px;">


<div>

<p style="font-size:10px;color:#94a3b8;text-transform:uppercase; text-decoration:underline">

Nature 
</p>


<p style="font-weight:700;font-size:12px">

{{ $bonCommande->nature ?? 'UNIFORMES SCOLAIRES' }}

</p>


</div>


</div>






{{-- TABLEAU --}}

<div class="table-scroll">

<table>


<thead>

<tr style="text-align: left;">

<th>Désignations</th>
<th>Taille / Classe </th>
<th style="text-align:center">
Qté
</th>

<th style="text-align:right">
P.U
</th>


<th style="text-align:right">
Valeur
</th>


</tr>

</thead>




<tbody>



@foreach($bonCommande->lignes as $index=>$ligne)


<tr>






<td style="font-weight:600">

{{ $ligne->libelle() }}

</td>



<td>

{{ $ligne->taille ?? '……………………………' }}
</td>





<td style="text-align:center;font-weight:bold">

{{ $ligne->quantite }}

</td>



<td style="text-align:right">

{{ number_format($ligne->prix_unitaire,0,',',' ') }}
FCFA

</td>




<td style="
text-align:right;
font-weight:700;
color:#185FA5;
">

{{ number_format($ligne->montant_ligne,0,',',' ') }}
FCFA

</td>



</tr>


@endforeach



</tbody>




<tfoot>


<tr style="background:#f8fafc">


<td colspan="2"
style="font-weight:800;text-align:left">

TOTAL 

</td>


<td style="font-weight:900;text-align:center">

{{ $bonCommande->lignes->sum('quantite') }}

</td>


<td colspan="1"></td>


<td style="
font-size:14px;
font-weight:900;
color:#0C447C;
text-align:right;
">

{{ number_format($bonCommande->montant,0,',',' ') }}
FCFA

</td>


</tr>



</tfoot>


</table>

</div>






@if($bonCommande->montant > 0)

<p style="
margin-top:20px;
font-size:12px;
font-style:italic;
">


Arrêté le présent bon de commande à la somme de :


<strong>

{{ mb_strtoupper(\App\Support\NombreEnLettres::enMontant($bonCommande->montant)) }}

</strong>


</p>


@endif

<p style="
margin-top:20px;
font-size:12px;
font-style:italic;
">


Condition de paiement :


<strong>

{{ $bonCommande->condition_paiement ?? 'VOIR CONTRAT' }}

</strong>


</p>



{{-- SIGNATURE --}}


<div class="signature-grid" style="margin-top:80px;">


<div></div>



<div style="text-align:center">


<p style="
border-top:1px solid #cbd5e1;
padding-top:10px;
font-size:11px;
">

LA DIRECTION

</p>


</div>


</div>





<p style="
font-size:9px;
color:#cbd5e1;
margin-top:40px;
text-align:center;
">

Bon de commande généré le {{ now()->format('d/m/Y à H:i') }} — MACOPRES

</p>



</div>


</body>

</html>