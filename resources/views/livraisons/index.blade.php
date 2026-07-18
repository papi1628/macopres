<x-app-layout>
<x-slot name="title">Livraisons — {{ $programme->ecole->nom }}</x-slot>

@if (session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#FCEBEB; color:#A32D2D; border-color:#F5C0C0">
        {{ session('error') }}
    </div>
@endif

<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="mobile-header flex items-center justify-between flex-wrap gap-4">
            <div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#DBFCE7; color:#166534">Livraisons</span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $programme->annee_scolaire }}</p>
            </div>
            <div class="mobile-actions flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('programmes.livraisons.suivi', $programme) }}" target="_blank"
                    class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center justify-center gap-1.5"
                    style="background:linear-gradient(135deg,#166534,#3B6D11)">
                        Tableau de suivi
                </a>
                <a href="{{ route('programmes.livraisons.create', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center justify-center gap-1.5"
                   style="background:linear-gradient(135deg,#166534,#3B6D11)">
                    + Nouvelle livraison
                </a>
                <a href="{{ route('programmes.show', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center">
                    ← Retour au programme
                </a>
            </div>
        </div>
    </div>

    {{-- LISTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">N° Bordereau</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Livreur</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Quantité livrée</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Document</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->livraisons->sortByDesc('date') as $livraison)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-[12px]" style="color:#166534">{{ $livraison->numero }}</td>
                            <td class="px-4 py-3 text-[12px] text-slate-600">{{ $livraison->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-[12px] text-slate-700">{{ $livraison->livreur ?? '–' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-[12px]">{{ $livraison->quantite }} pièce(s)</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('programmes.livraisons.imprimer', $livraison) }}" target="_blank" class="text-[11px] font-semibold" style="color:#166534">Voir / Imprimer</a>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">

                                    @php
                                        $lignesModification = $livraison->lignes->map(function($l){
                                            return [
                                                'id' => $l->id,
                                                'libelle' => $l->ligneBonCommande->libelle(),
                                                'taille' => $l->ligneBonCommande->taille,
                                                'quantite' => $l->quantite_livree,
                                            ];
                                        });
                                    @endphp

                                    <button type="button"
                                        onclick='ouvrirModification(
                                            {{ $livraison->id }},
                                            "{{ $livraison->date->format("Y-m-d") }}",
                                            @json($livraison->livreur),
                                            @json($lignesModification)
                                        )'
                                        class="text-blue-500 text-[11px]">
                                        Modifier
                                    </button>

                                    <button type="button"
                                            onclick="supprimerLivraison({{ $livraison->id }}, this)"
                                            class="text-red-400 hover:text-red-600 text-[11px]">
                                        Supprimer
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune livraison enregistrée</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalModification"
     class="hidden fixed inset-0 bg-black/30 z-50 flex items-center justify-center">

    <div class="bg-white rounded-2xl p-5 w-full max-w-sm">

        <h3 class="text-sm font-bold text-slate-800 mb-4">
            Modifier la livraison
        </h3>

        <input type="hidden" id="livraison_id">

        <label class="text-[10px] uppercase text-slate-400">
            Date
        </label>

        <input id="livraison_date"
               type="date"
               class="w-full h-9 border rounded-xl px-3 mb-3">


        <label class="text-[10px] uppercase text-slate-400">
            Livreur
        </label>

        <input id="livraison_livreur"
               class="w-full h-9 border rounded-xl px-3">

        <div id="edit_lignes"></div>

        <div class="flex justify-end gap-2 mt-5">

            <button onclick="fermerModification()"
                    class="h-9 px-4 rounded-xl border">
                Annuler
            </button>

            <button onclick="modifierLivraison()"
                    class="h-9 px-4 rounded-xl text-white"
                    style="background:#166534">
                Enregistrer
            </button>

        </div>

    </div>
</div>

<style>

@media (max-width:640px){

    .mobile-header{
        flex-direction:column;
        align-items:flex-start !important;
    }

    .mobile-actions{
        width:100%;
        display:flex;
        flex-wrap:wrap;
        gap:8px;
    }

    .mobile-actions a{
        flex:1 1 calc(50% - 4px);
        justify-content:center;
        white-space:nowrap;
    }

    .mobile-actions a:last-child{
        flex:1 1 100%;
    }

}

@media(max-width:420px){

    .mobile-actions a{
        flex:1 1 100%;
    }

}
@media(max-width:640px){

    .page{
        overflow-x:hidden;
    }

    .bg-white.rounded-2xl{
        border-radius:16px;
    }

}

</style>

<script>

    function supprimerLivraison(id, bouton){

        if(!confirm('Supprimer cette livraison ?')) return;

        fetch(`/livraisons/${id}`,{
            method:'DELETE',
            headers:{
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
                'Accept':'application/json'
            }
        })
        .then(res=>res.json())
        .then(data=>{

            bouton.closest('tr').remove();

        });

    }



    function ouvrirModification(id,date,livreur,lignes){

        document.getElementById('livraison_id').value=id;
        document.getElementById('livraison_date').value=date;
        document.getElementById('livraison_livreur').value=livreur ?? '';

        let html='';

        lignes.forEach(ligne=>{

            html += `
                <div class="mt-3">
                    <label class="text-[10px] uppercase text-slate-400">
                        ${ligne.libelle} ${ligne.taille ?? ''}
                    </label>

                    <input 
                        type="number"
                        min="0"
                        data-ligne="${ligne.id}"
                        value="${ligne.quantite}"
                        class="quantite-ligne w-full h-9 border rounded-xl px-3"
                    >
                </div>
            `;

        });


        document.getElementById('edit_lignes').innerHTML=html;


        document.getElementById('modalModification')
            .classList.remove('hidden');
    }


    function fermerModification(){

        document.getElementById('modalModification')
            .classList.add('hidden');

    }



    function modifierLivraison(){

        let id = document.getElementById('livraison_id').value;

        let quantites = {};

        document.querySelectorAll('.quantite-ligne')
        .forEach(input => {

            quantites[input.dataset.ligne] = input.value;

        });


        fetch(`/livraisons/${id}`, {

            method:'PUT',

            headers:{
                'Content-Type':'application/json',
                'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,
                'Accept':'application/json'
            },

            body:JSON.stringify({

                date: document.getElementById('livraison_date').value,

                livreur: document.getElementById('livraison_livreur').value,

                quantites: quantites

            })

        })
        .then(res => res.json())
        .then(data => {

            if(data.success){

                fermerModification();

                location.reload();

            }

        })
        .catch(error => {

            console.error(error);

        });

    }

</script>
</x-app-layout>