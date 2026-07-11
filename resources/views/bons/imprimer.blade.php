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
    width:800px;
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
    padding:10px;
    font-size:11px;
    text-transform:uppercase;
}


td {
    padding:8px;
    font-size:12px;
    border-bottom:1px solid #e2e8f0;
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
    }

}

</style>

</head>


<body>


<div class="toolbar bg-white border-b px-5 py-3 flex justify-between">

<p class="font-bold text-slate-700">
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
justify-content:space-between;
align-items:center;
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
NUMERO
</span>
{{ $bonCommande->numero }}
</p>




<p>
<span style="font-weight:700;color:#64748b">
DATE
</span>
{{ $bonCommande->date->format('d/m/Y') }}
</p>


</div>


</div>






{{-- CLIENT BENEFICIAIRE --}}


<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:20px;
margin-bottom:20px;
">


<div style="
background:#f8fafc;
padding:15px;
border-radius:12px;
">


<p style="
font-size:10px;
color:#94a3b8;
text-transform:uppercase;
">
CLIENT
</p>


<p style="
font-weight:900;
font-size:15px;
">

{{ mb_strtoupper($ecole->nom) }}

</p>


<p style="font-size:11px;color:#64748b">

{{ $ecole->adresse ?? '' }}

<br>

{{ $ecole->telephone ?? $ecole->contact_telephone ?? '' }}

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
text-transform:uppercase;
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


<div style="
display:grid;
grid-template-columns:1fr 1fr;
margin-bottom:20px;
">


<div>

<p style="font-size:10px;color:#94a3b8;text-transform:uppercase">
Nature
</p>


<p style="font-weight:700;font-size:12px">

{{ $bonCommande->nature ?? 'UNIFORMES SCOLAIRES' }}

</p>


</div>



<div>

<p style="font-size:10px;color:#94a3b8;text-transform:uppercase">
Condition de paiement
</p>


<p style="font-weight:700;font-size:12px">

{{ $bonCommande->condition_paiement ?? 'VOIR CONTRAT' }}

</p>


</div>


</div>






{{-- TABLEAU --}}


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

{{-- SIGNATURES --}}


<div style="
display:grid;
grid-template-columns:1fr 1fr;
gap:60px;
margin-top:80px;
">


<div>



</div>




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









</div>


</body>

</html>