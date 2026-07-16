
<x-app-layout>
<x-slot name="title">Contrat — {{ $programme->ecole->nom }}</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif

@php $contrat = $programme->contrat; @endphp

<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <span id="statut-contrat" class="text-[10px] sm:text-[11px] font-semibold px-2 py-0.5 rounded-full"
                      style="background:{{ $contrat->estSigne() ? '#EAF3DE' : '#FEF6E4' }}; color:{{ $contrat->estSigne() ? '#3B6D11' : '#854F0B' }}">
                    Contrat — {{ $contrat->libelleStatut() }}
                </span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">Programme {{ $programme->annee_scolaire }} · Basé sur {{ $contrat->bonCommande->numero ?? '–' }}</p>
            </div>
            <div class="flex flex-col sm:flex-row w-full lg:w-auto gap-2">
                @if(!$contrat->estSigne())

                <button
                    type="button"
                    onclick="marquerSigne(
                        this,
                        '{{ route('programmes.contrat.signer', $programme) }}'
                    )"
                    class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white w-full sm:w-auto justify-center"
                    style="background:linear-gradient(135deg,#3B6D11,#5A9A1E)">

                    Marquer comme signé

                </button>

                @endif
                <a href="{{ route('programmes.contrat.imprimer', $programme) }}" target="_blank"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center gap-1.5 w-full sm:w-auto justify-center" style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Imprimer le contrat
                </a>
                <a href="{{ route('programmes.show', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center w-full sm:w-auto justify-center">
                    ← Retour au programme
                </a>
            </div>
        </div>
    </div>

    {{-- ENGAGEMENT (auto, lecture seule) --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Article 1 — Engagement</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Généré automatiquement à partir des articles du premier bon de commande</p>
        </div>
        <div class="p-4 sm:p-5">
            <p class="text-[12px] text-slate-700 whitespace-pre-line break-words">{{ $contrat->description_engagement ?: 'Aucun article pour le moment.' }}</p>
        </div>
    </div>

    {{-- MONTANT (auto, lecture seule) --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Article 2 — Facturation</h3>
        </div>
        <div class="p-4 sm:p-5">
            <p class="text-[20px] font-black" style="color:#0C447C">{{ number_format($contrat->montant_total, 0, ',', ' ') }} FCFA</p>
            @if($contrat->montant_total > 0)
                <p class="text-[11px] italic text-slate-500 mt-1">{{ \App\Support\NombreEnLettres::enMontant($contrat->montant_total) }}</p>
            @endif
        </div>
    </div>

    {{-- ÉCHÉANCIER DE PAIEMENT --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <div>
                <h3 class="text-[12px] font-semibold text-slate-800">Article 4 — Échéancier de paiement</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Les montants et dates saisis ici apparaîtront tels quels sur le contrat imprimé</p>
            </div>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Ajouter une échéance'"></span>
            </button>
        </div>

        <div x-show="open" class="p-4 sm:p-5 border-b border-slate-100" style="background:#f8fafc">
            <form
                id="form-echeance"
                method="POST"
                action="{{ route('programmes.contrat.echeances.store', $programme) }}"
                onsubmit="ajouterEcheance(event,this)"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @csrf
                <input type="date" name="date_prevue" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="number" step="0.01" name="montant_prevu" placeholder="Montant (ex : 1500000)" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter</button>
            </form>
            <p class="text-[10px] text-slate-400 mt-2">Astuce : évitez les montants avec virgules (ex : préférez 1 500 000 à 1 250 333).</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[650px] w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Versement</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date prévue</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Montant</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody id="liste-echeances" class="divide-y divide-slate-50">
                    @forelse($programme->echeancesPaiement as $ech)
                        <tr id="echeance-{{ $ech->id }}">

                            <td class="numero-echeance px-4 py-2.5 text-[12px] font-semibold text-slate-700 capitalize">
                                {{ \App\Support\NombreEnLettres::ordinal($ech->numero_versement) }}
                            </td>


                            <td class="date-echeance px-4 py-2.5 text-[12px] text-slate-600">
                                {{ $ech->date_prevue->format('d/m/Y') }}
                            </td>


                            <td class="montant-echeance px-4 py-2.5 text-right font-semibold text-[12px]">
                                {{ number_format($ech->montant_prevu,0,',',' ') }} F
                            </td>


                            <td class="px-4 py-2.5 text-right">

                                <button
                                    type="button"
                                    onclick="modifierEcheance({{ $ech->id }})"
                                    class="text-blue-500 text-[11px] mr-3">

                                    Modifier

                                </button>


                                <button
                                    type="button"
                                    onclick="supprimerEcheance(
                                        this,
                                        '{{ route('programmes.contrat.echeances.destroy', [$programme, $ech]) }}',
                                        {{ $ech->id }}
                                    )"
                                    class="text-red-400 text-[11px]">

                                    Supprimer

                                </button>

                            </td>

                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune échéance définie</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:#f8fafc">
                        <td colspan="2" class="px-4 py-2.5 text-[11px] font-bold text-slate-600">
                            TOTAL ÉCHELONNÉ
                        </td>

                        <td id="total-echeances"
                            class="px-4 py-2.5 text-right font-bold text-[12px]"
                            style="color:#0C447C">
                            {{ number_format($programme->echeancesPaiement->sum('montant_prevu'),0,',',' ') }} F
                        </td>

                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- INFOS À COMPLÉTER --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Représentant, délai et signature</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Ces informations ne peuvent pas être déduites automatiquement</p>
        </div>
        <form method="POST" action="{{ route('programmes.contrat.update', $programme) }}" class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Représentant de l'école</label>
                <input type="text" name="representant_client" value="{{ $contrat->representant_client }}"
                       class="w-full h-9 border border-slate-200 rounded-xl px-3 text-[13px] focus:outline-none focus:border-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Rôle du représentant</label>
                <input type="text" name="representant_client_role" value="{{ $contrat->representant_client_role }}" placeholder="Ex : Directrice de l'école"
                       class="w-full h-9 border border-slate-200 rounded-xl px-3 text-[13px] focus:outline-none focus:border-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Délai de livraison (date)</label>
                <input type="date" name="date_limite_livraison" value="{{ $contrat->date_limite_livraison?->format('Y-m-d') }}"
                       class="w-full h-9 border border-slate-200 rounded-xl px-3 text-[13px] focus:outline-none focus:border-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Délai de livraison (texte)</label>
                <input type="text" name="delai_livraison_texte" value="{{ $contrat->delai_livraison_texte }}" placeholder="Ex : avant la rentrée scolaire"
                       class="w-full h-9 border border-slate-200 rounded-xl px-3 text-[13px] focus:outline-none focus:border-blue-400">
            </div>
            <div>
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date de réalisation du contrat</label>
                <input type="date" name="date_signature" value="{{ $contrat->date_signature?->format('Y-m-d') }}"
                       class="w-full h-9 border border-slate-200 rounded-xl px-3 text-[13px] focus:outline-none focus:border-blue-400">
            </div>
            <div class="sm:col-span-2 flex justify-stretch sm:justify-end">
               <button class="w-full sm:w-auto h-9 px-5 rounded-xl text-[13px] font-bold text-white" type="submit" class="h-9 px-5 rounded-xl text-sm font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    
const urlSuppressionEcheance = "{{ url('/programmes/'.$programme->id.'/echeances') }}";

const programmeId = {{ $programme->id }};

const urlBaseEcheance = "{{ url('/programmes/'.$programme->id.'/echeances') }}";

function ligneEcheance(e){

    return `
        <tr id="echeance-${e.id}">
            <td class="numero-echeance px-4 py-2.5 text-[12px] font-semibold text-slate-700">
                ${e.numero_versement}
            </td>

            <td class="date-echeance px-4 py-2.5 text-[12px] text-slate-600">
                ${e.date_prevue}
            </td>

            <td class="montant-echeance px-4 py-2.5 text-right font-semibold text-[12px]">
                ${Number(e.montant_prevu).toLocaleString('fr-FR')} F
            </td>

            <td class="px-4 py-2.5 text-right">

                <button
                    type="button"
                    onclick="modifierEcheance(${e.id})"
                    class="text-blue-500 text-[11px] mr-3">

                    Modifier

                </button>


                <button
                    type="button"
                    class="text-red-400 hover:text-red-600 text-[11px]"
                    onclick="supprimerEcheance(
                        this,
                        '${urlSuppressionEcheance}/${e.id}',
                        ${e.id}
                    )">

                    Supprimer

                </button>

            </td>

        </tr>
    `;

}

async function ajouterEcheance(event, form){

    event.preventDefault();

    const bouton = form.querySelector('button[type=submit]');

    bouton.disabled = true;
    bouton.innerHTML = "Ajout...";

    try{

        const response = await fetch(form.action,{

            method:'POST',

            headers:{

                'Accept':'application/json',

                'X-CSRF-TOKEN':
                document.querySelector('meta[name=csrf-token]').content

            },

            body:new FormData(form)

        });

        const json = await response.json();

        if(!response.ok || !json.success){

            throw new Error();

        }

        document
            .getElementById("liste-echeances")
            .insertAdjacentHTML("beforeend", ligneEcheance(json.echeance));

        document.getElementById("total-echeances").innerHTML =
            Number(json.total).toLocaleString('fr-FR')+" F";

        form.reset();

    }

    catch(e){

        console.error(e);

        alert("Impossible d'ajouter l'échéance.");

    }

    finally{

        bouton.disabled=false;

        bouton.innerHTML="Ajouter";

    }

}

async function supprimerEcheance(button, url, id){

    if(!confirm("Supprimer cette échéance ?"))
        return;

    button.disabled = true;

    try{

        const response = await fetch(url,{

            method:'DELETE',

            headers:{

                Accept:'application/json',

                'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content

            }

        });

        const json = await response.json();

        if(!response.ok || !json.success){

            throw new Error();

        }

        document.getElementById("echeance-"+id)?.remove();

        document.getElementById("total-echeances").innerHTML =
            Number(json.total).toLocaleString('fr-FR')+" F";

        json.echeances.forEach(e=>{

            document
                .querySelector("#echeance-"+e.id+" td")
                .innerHTML = e.numero;

        });

    }

    catch(e){

        console.error(e);

        alert("Impossible de supprimer cette échéance.");

        button.disabled = false;

    }

}

async function marquerSigne(button, url){

    if(!confirm("Marquer ce contrat comme signé ?"))
        return;


    button.disabled = true;
    button.innerHTML = "Signature...";


    try{

        const response = await fetch(url,{

            method:'PATCH',

            headers:{

                Accept:'application/json',

                'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]').content

            }

        });


        const json = await response.json();


        if(!response.ok || !json.success){

            throw new Error();

        }


        // Remplacer le badge du statut
        const badge = document.querySelector("#statut-contrat");

        if(badge){

            badge.innerHTML = "Contrat — " + json.libelle;

            badge.style.background = "#EAF3DE";
            badge.style.color = "#3B6D11";

        }


        // Retirer le bouton après signature
        button.remove();


    }

    catch(e){

        console.error(e);

        alert("Impossible de signer le contrat.");

        button.disabled = false;

        button.innerHTML = "Marquer comme signé";

    }

}

function convertirDate(date){

    const morceaux = date.split('/');

    return `${morceaux[2]}-${morceaux[1]}-${morceaux[0]}`;

}

function modifierEcheance(id){

    const ligne=document.getElementById("echeance-"+id);


    const date =
        ligne.querySelector(".date-echeance").innerText;


    const montant =
        ligne.querySelector(".montant-echeance")
        .innerText
        .replace(/\s|F/g,'');


    ligne.querySelector(".date-echeance").innerHTML = `
        <input 
            type="date"
            id="date-${id}"
            value="${convertirDate(date)}"
            class="h-8 border rounded-lg text-xs px-2">
    `;


    ligne.querySelector(".montant-echeance").innerHTML = `
        <input 
            type="number"
            id="montant-${id}"
            value="${montant}"
            class="h-8 border rounded-lg text-xs px-2">
    `;


    ligne.querySelector("td:last-child").innerHTML=`

        <button
        onclick="enregistrerEcheance(${id})"
        class="text-green-600 text-[11px]">
        Enregistrer
        </button>

    `;

}

async function enregistrerEcheance(id){

    const ligne = document.getElementById("echeance-"+id);

    const date = document.getElementById("date-"+id).value;

    const montant = document.getElementById("montant-"+id).value;


    try{

        const response = await fetch(
            `/programmes/${programmeId}/echeances/${id}`,
            {

                method:'PUT',

                headers:{

                    Accept:'application/json',

                    'Content-Type':'application/json',

                    'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content

                },

                body:JSON.stringify({

                    date_prevue:date,

                    montant_prevu:montant

                })

            }
        );


        console.log("STATUS :", response.status);

        const json = await response.json();

        console.log("JSON :", json);


        if(!response.ok || !json.success){

            throw new Error();

        }


        // Mise à jour de la date
        ligne.querySelector(".date-echeance").innerHTML =
            json.echeance.date;


        // Mise à jour du montant
        ligne.querySelector(".montant-echeance").innerHTML =
            Number(json.echeance.montant)
                .toLocaleString('fr-FR')+" F";  


        // Mise à jour du total
        document.getElementById("total-echeances").innerHTML =
            Number(json.total).toLocaleString('fr-FR')+" F";


        // Remettre les boutons
        ligne.querySelector("td:last-child").innerHTML = `

            <button
                type="button"
                onclick="modifierEcheance(${id})"
                class="text-blue-500 text-[11px] mr-3">

                Modifier

            </button>


            <button
                type="button"
                onclick="supprimerEcheance(
                    this,
                    '${urlBaseEcheance}/${id}',
                    ${id}
                )"
                class="text-red-400 hover:text-red-600 text-[11px]">

                Supprimer

            </button>

        `;


    }

    catch(e){

        console.error(e);

        alert("Impossible de modifier l'échéance.");

    }

}
</script>
</x-app-layout>