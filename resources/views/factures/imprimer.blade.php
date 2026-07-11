<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Facture {{ $bonCommande->facture->numero }}</title>

@vite(['resources/css/app.css'])

<style>
    body {
        font-family: 'Inter', Arial, sans-serif;
        background:#f1f5f9;
        color:#1e293b;
    }

    .page {
        width: 800px;
        margin:auto;
        background:white;
        padding:35px 45px;
        min-height:1100px;
    }

    table {
        width:100%;
        border-collapse:collapse;
    }

    th {
        background:#0C447C;
        color:white;
        font-size:11px;
        text-transform:uppercase;
        padding:10px;
    }

    td {
        padding:9px;
        border-bottom:1px solid #e2e8f0;
        font-size:12px;
    }

    @media print {

        body {
            background:white;
        }

        .toolbar {
            display:none;
        }

        .page {
            width:auto;
            padding:0;
        }
    }

</style>

</head>


<body>


@php

$facture = $bonCommande->facture;
$ecole = $bonCommande->programme->ecole;

@endphp



<div class="toolbar bg-white border-b px-5 py-3 flex justify-between">

<p class="font-bold text-slate-700">
Facture {{ $facture->numero }}
</p>


<button onclick="window.print()"
class="px-5 py-2 rounded-xl text-white text-sm font-bold"
style="background:#185FA5">

Imprimer

</button>


</div>




<div class="page">



{{-- ENTETE ENTREPRISE --}}

<div style="
text-align:center;
border-bottom:3px solid #0C447C;
padding-bottom:20px;
margin-bottom:25px;
">


<h1 style="
font-size:30px;
font-weight:900;
letter-spacing:1px;
color:#0C447C;
margin:0;
">
MACOPRES
</h1>




<p style="
font-size:11px;
margin-top:15px;
color:#475569;
">

Siège Social : DAKAR (SENEGAL), 14 Cité Fadia

</p>


<p style="
font-size:11px;
color:#475569;
">

RCCM : SN.DKR.2017.B.12286
&nbsp;&nbsp; | &nbsp;&nbsp;
NINEA : 006363775-2T2

</p>



<p style="
font-size:11px;
color:#475569;
">

www.macopresgroup.sn
&nbsp;&nbsp; - &nbsp;&nbsp;
contact@macopres.sn
&nbsp;&nbsp; - &nbsp;&nbsp;
+221 33 855 16 70 / +221 77 659 42 18

</p>


</div>





{{-- BLOC FACTURE --}}

<div style="
display:flex;
justify-content:space-between;
align-items:center;
background:#f8fafc;
border-radius:14px;
padding:18px 22px;
margin-bottom:25px;
">


<div>

<h2 style="
font-size:20px;
font-weight:900;
color:#0C447C;
margin:0;
">
FACTURE
</h2>





</div>




<div style="
display:grid;
grid-template-columns:auto auto;
gap:8px 20px;
font-size:10px;
">


<span style="font-weight:700;color:#64748b">
NUMERO
</span>

<span style="font-weight:900;color:#0C447C">
{{ $facture->numero }}
</span>



<span style="font-weight:700;color:#64748b">
DATE
</span>

<span>
{{ $facture->date->format('d/m/Y') }}
</span>








<span style="font-weight:700;color:#64748b">
COMMERCIAL
</span>

<span>
M. BA
</span>


</div>



</div>




{{-- CLIENT --}}

<div style="
background:#f8fafc;
border-radius:12px;
padding:15px;
margin-bottom:25px;
">


<div style="display:flex;justify-content:space-between">


<div>

<p class="text-xs text-slate-400 uppercase">
CLIENT
</p>


<p style="font-size:15px;font-weight:800">
{{ mb_strtoupper($ecole->nom) }}
</p>


</div>



<div>

<p class="text-xs text-slate-400 uppercase">
CONTACT
</p>


<p style="font-weight:700">

{{ $ecole->contact_nom ?? 'M. DIACK' }}

{{ $ecole->contact_telephone ?? $ecole->telephone ?? '' }}

</p>


</div>



</div>


</div>






{{-- ARTICLES --}}


<table>


<thead>

<tr style="text-align: left;">

<th>Références</th>

<th>Désignations</th>

<th style="text-align:center">
Quantités
</th>


<th style="text-align:right">
P.Unitaires
</th>


<th style="text-align:right">
Montants HT
</th>


</tr>

</thead>



<tbody>

@foreach($bonCommande->lignes as $index => $ligne)

<tr>

@if($index === 0)
<td rowspan="{{ $bonCommande->lignes->count() }}"
    style="font-weight:700; vertical-align:middle;">
    Macopres confection
</td>
@endif


<td style="font-weight:700">
    {{ $ligne->libelle() }}
</td>


<td style="text-align:center">
    {{ $ligne->quantite }}
</td>


<td style="text-align:right">
    {{ number_format($ligne->prix_unitaire,0,',',' ') }}
    FCFA
</td>


<td style="text-align:right;font-weight:700;color:#185FA5;">
    {{ number_format($ligne->montant_ligne,0,',',' ') }}
    FCFA
</td>


</tr>

@endforeach

</tbody>



<tfoot>


<tr>

<td colspan="4"
style="text-align:left;font-weight:bold">

Total HT

</td>


<td style="
text-align:right;
font-weight:900;
">

{{ number_format($facture->montant,0,',',' ') }}
FCFA

</td>


</tr>



<tr style="background:#f8fafc">


<td colspan="4"
style="text-align:left;font-weight:bold">

Total TTC

</td>


<td style="
text-align:right;
font-size:14px;
font-weight:900;
color:#0C447C;
">

{{ number_format($facture->montant,0,',',' ') }}
FCFA

</td>


</tr>


</tfoot>



</table>







@if($facture->montant > 0)


<p style="
margin-top:20px;
font-size:12px;
font-style:italic;
">

Arrêtée à la somme de :

<strong>

{{ mb_strtoupper(\App\Support\NombreEnLettres::enMontant($facture->montant)) }}

</strong>


</p>


@endif






@if($bonCommande->condition_paiement)


<div style="
margin-top:20px;
font-size:12px;
">


<strong>
Condition de paiement :
</strong>


{{ mb_strtoupper($bonCommande->condition_paiement) }}


</div>


@endif






<div style="
margin-top:80px;
text-align:right;
">


<p style="font-size:12px;color:#64748b">

LE SERVICE COMMERCIAL

</p>






</div>










</div>



</body>

</html>