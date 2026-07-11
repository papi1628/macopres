<x-app-layout>
<x-slot name="title">Factures — {{ $programme->ecole->nom }}</x-slot>

@if(session('success'))
<div x-data="{show:true}" x-show="show"
     x-init="setTimeout(()=>show=false,3000)"
     class="mb-4 px-4 py-3 rounded-xl text-sm font-medium border"
     style="background:#EAF3DE;color:#3B6D11;border-color:#C3E6A0">
    {{ session('success') }}
</div>
@endif


@if(session('error'))
<div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium border"
     style="background:#FCEBEB;color:#A32D2D;border-color:#F5C0C0">
    {{ session('error') }}
</div>
@endif



<div class="space-y-5">


{{-- HEADER --}}
<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">

<div class="flex items-center justify-between flex-wrap gap-4">

<div>

<span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
style="background:#EAF3DE;color:#3B6D11">
Factures
</span>


<h2 class="text-[17px] font-bold text-slate-800 mt-1">
{{ $programme->ecole->nom }}
</h2>


<p class="text-[11px] text-slate-400 mt-1">
{{ $programme->annee_scolaire }}
</p>

</div>



<a href="{{ route('programmes.show',$programme) }}"
class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
← Retour au programme
</a>


</div>

</div>



<h3 class="text-[13px] font-bold text-slate-800">
Factures ({{ $programme->bonsCommande->filter(fn($b)=>$b->facture)->count() }})
</h3>




@if($programme->bonsCommande->filter(fn($b)=>$b->facture)->isEmpty())


<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">

<p class="text-[14px] font-semibold text-slate-600">
Aucune facture disponible
</p>

<p class="text-[12px] text-slate-400 mt-1">
Les factures seront générées automatiquement.
</p>

</div>


@else



<div class="relative pl-8">


<div class="absolute left-[15px] top-2 bottom-2 w-px"
style="background:#E2E8F0">
</div>



@foreach($programme->bonsCommande as $i=>$bon)

@if($bon->facture)


<div x-data="{open:false}" class="relative mb-5">



{{-- NUMERO --}}
<div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black text-white"
style="background:linear-gradient(135deg,#3B6D11,#5A9A1E)">
{{ $i+1 }}
</div>




<div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">





{{-- PARTIE HAUTE --}}
<div class="flex items-center justify-between px-5 py-4 cursor-pointer"
@click="open=!open">


<div>


<p class="text-[11px] font-semibold text-slate-400">
Facture
</p>


<p class="font-mono font-bold text-[14px]"
style="color:#3B6D11">

{{ $bon->facture->numero }}

</p>


<p class="text-[11px] text-slate-400 mt-1">
Commande : {{ $bon->numero }}
</p>


</div>




<div class="grid grid-cols-3 gap-6 text-right">


<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Date
</p>


<p class="text-[12px] font-semibold text-slate-700">
{{ $bon->facture->date->format('d/m/Y') }}
</p>

</div>




<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Montant
</p>


<p class="text-[13px] font-bold"
style="color:#3B6D11">

{{ number_format($bon->facture->montant,0,',',' ') }} F

</p>

</div>




<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Articles
</p>


<p class="text-[12px] font-semibold">

{{ $bon->lignes->count() }}

</p>

</div>


</div>




<a href="{{ route('programmes.bons.facture',$bon) }}"
target="_blank"
@click.stop
class="ml-4 h-8 px-3 rounded-lg text-[11px] font-semibold text-white flex items-center"
style="background:linear-gradient(135deg,#3B6D11,#5A9A1E)">
Imprimer
</a>


</div>








{{-- PARTIE QUI S'OUVRE AU MILIEU --}}
<div x-show="open"
x-collapse
class="border-t border-slate-100 px-5 py-4"
style="background:#ffffff">


<p class="text-[11px] font-bold text-slate-700 mb-3">
Détails des articles
</p>



<div class="overflow-x-auto">

<table class="w-full text-[12px]">


<thead>

<tr class="border-b border-slate-100">

<th class="text-left py-2 text-[9px] uppercase text-slate-400">
Désignation
</th>


<th class="text-center py-2 text-[9px] uppercase text-slate-400">
Quantité
</th>

<th class="text-center py-2 text-[9px] uppercase text-slate-400">
Prix Unitaire
</th>

<th class="text-right py-2 text-[9px] uppercase text-slate-400">
Montant
</th>


</tr>

</thead>



<tbody>


@foreach($bon->lignes as $ligne)


<tr class="border-b border-slate-50">


<td class="py-2 font-semibold text-slate-700">
{{ $ligne->libelle() }}
</td>


<td class="py-2 text-center">
{{ $ligne->quantite }}
</td>

<td class="py-2 text-center">
{{ number_format($ligne->prix_unitaire,0,',',' ') }}
    FCFA
</td>


<td class="py-2 text-right font-bold"
style="color:#3B6D11">

{{ number_format($ligne->montant_ligne,0,',',' ') }} FCFA

</td>


</tr>


@endforeach


</tbody>

<tfoot>
<tr>
    <td colspan="3"
        class="py-2 font-bold text-slate-700">
        Total
    </td>

    <td class="py-2 text-right font-black"
        style="color:#3B6D11">

        {{ number_format($bon->facture->montant,0,',',' ') }} FCFA

    </td>
</tr>
</tfoot>


</table>


</div>


</div>







{{-- PARTIE BASSE TOUJOURS VISIBLE --}}
<div class="border-t border-slate-100 px-5 py-3"
style="background:#f8fafc">


<div class="grid grid-cols-2 sm:grid-cols-3 gap-4">



<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Commande liée
</p>

<p class="text-[12px] text-slate-700">
{{ $bon->numero }}
</p>

</div>



<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Montant en lettres
</p>

<p class="text-[11px] italic text-slate-500">

{{ \App\Support\NombreEnLettres::enMontant($bon->facture->montant) }}

</p>

</div>




<div>

<p class="text-[9px] uppercase font-semibold text-slate-400">
Statut
</p>


<span class="inline-flex px-2 py-1 rounded-full text-[10px] font-semibold"
style="background:#EAF3DE;color:#3B6D11">

Générée

</span>


</div>



</div>


</div>



</div>



</div>


@endif

@endforeach



</div>


@endif



</div>


</x-app-layout>