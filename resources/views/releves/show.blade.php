<x-app-layout>
<x-slot name="title">Relevé de compte — {{ $programme->ecole->nom }}</x-slot>

<div id="alerte-zone"></div>

@php
    $totalFactures = $programme->bonsCommande->sum(fn($b) => $b->facture->montant ?? 0);
    $totalVersements = $programme->paiements->sum('montant');
    $resteAPayer = $totalFactures - $totalVersements;
    $estSolde = $totalFactures > 0 && $resteAPayer <= 0.01;
@endphp

<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between flex-wrap gap-4 mb-4">
            <div>
                <span id="badge-statut" class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                      style="background:{{ $estSolde ? '#EAF3DE' : '#FEF6E4' }}; color:{{ $estSolde ? '#3B6D11' : '#854F0B' }}">
                    {{ $estSolde ? 'SOLDÉ' : 'Relevé de compte' }}
                </span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $programme->annee_scolaire }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('programmes.releve.imprimer', $programme) }}" target="_blank"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center gap-1.5"
                   style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Imprimer le relevé
                </a>
                <a href="{{ route('programmes.show', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                    ← Retour au programme
                </a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total facturé</p>
                <p id="total-factures" class="text-[18px] font-black" style="color:#0C447C">{{ number_format($totalFactures, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total versé</p>
                <p id="total-verse" class="text-[18px] font-black" style="color:#3B6D11">{{ number_format($totalVersements, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Reste à payer</p>
                <p id="reste-a-payer" class="text-[18px] font-black" style="color:{{ $resteAPayer > 0.01 ? '#A32D2D' : '#3B6D11' }}">
                    {{ $estSolde ? 'Soldé' : number_format($resteAPayer, 0, ',', ' ') . ' F' }}
                </p>
            </div>
        </div>
    </div>

    {{-- VERSEMENTS --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Versements</h3>
            <button @click="open = !open" id="btn-toggle-ajout" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Enregistrer un versement'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form id="form-ajout-versement" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <input type="number" step="0.01" name="montant" placeholder="Montant *" required
                       class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] sm:col-span-2">
                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                       class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <select name="mode_paiement" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] bg-white">
                    <option value="espece">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                    <option value="wave">Wave</option>
                    <option value="orange_money">Orange Money</option>
                    <option value="agent_mandate">Agent mandaté</option>
                </select>
                <input type="text" name="reference" placeholder="Référence (optionnel)"
                       class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] sm:col-span-3"> 
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Enregistrer
                </button>
            </form>
            <p class="text-[10px] text-slate-400 mt-2">Seul le montant est obligatoire. Il ne peut pas dépasser le reste à payer.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Montant</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Mode</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Référence</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody id="corps-versements" class="divide-y divide-slate-50">
                    @forelse($programme->paiements->sortByDesc('date') as $paiement)
                        <tr id="versement-{{ $paiement->id }}"
                            data-date="{{ $paiement->date->format('Y-m-d') }}"
                            data-montant="{{ $paiement->montant }}"
                            data-mode="{{ $paiement->mode_paiement }}"
                            data-reference="{{ $paiement->reference }}">
                            <td class="py-2.5 px-4 text-[12px] text-slate-600">{{ $paiement->date->format('d/m/Y') }}</td>
                            <td class="py-2.5 px-4 text-right font-semibold text-[12px]" style="color:#3B6D11">{{ number_format($paiement->montant, 0, ',', ' ') }} F</td>
                            <td class="py-2.5 px-4 text-[12px] text-slate-500">{{ $paiement->modeLabel() }}</td>
                            <td class="py-2.5 px-4 text-[12px] text-slate-500">{{ $paiement->reference ?? '–' }}</td>
                            <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                <button type="button" onclick="modifierVersement({{ $paiement->id }})" class="text-blue-400 hover:text-blue-600 text-[20px] mr-2" title="Modifier">✎</button>
                                <button type="button" onclick="supprimerVersement({{ $paiement->id }})" class="text-red-400 hover:text-red-600 text-[20px]" title="Supprimer">&times;</button>
                            </td>
                        </tr>
                    @empty
                        <tr id="ligne-vide"><td colspan="5" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucun versement enregistré</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RAPPEL DES FACTURES --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Factures</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">N° Facture</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->bonsCommande as $bon)
                        @if($bon->facture)
                            <tr>
                                <td class="px-4 py-2.5 font-mono font-bold text-[12px]" style="color:#0C447C">{{ $bon->facture->numero }}</td>
                                <td class="px-4 py-2.5 text-[12px] text-slate-600">{{ $bon->facture->date->format('d/m/Y') }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-[12px]">{{ number_format($bon->facture->montant, 0, ',', ' ') }} F</td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune facture</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>

body{
    overflow-x:hidden;
}


/* ==========================
   MOBILE
========================== */

@media(max-width:768px){


    /* HEADER */

    .bg-white.rounded-2xl > .flex.items-center.justify-between{

        flex-direction:column;
        align-items:flex-start;

    }


    .bg-white.rounded-2xl > .flex.items-center.justify-between > div:last-child{

        width:100%;
        display:flex;
        flex-direction:column;

    }


    .bg-white.rounded-2xl > .flex.items-center.justify-between > div:last-child a{

        width:100%;
        justify-content:center;

    }



    /* CARTES RESUME */

    .grid.grid-cols-3{

        grid-template-columns:1fr;

        gap:10px;

    }


    /* TITRES */

    h2{

        font-size:15px !important;

    }



    /* FORMULAIRE VERSEMENT */

    #form-ajout-versement{

        display:flex;
        flex-direction:column;

    }


    #form-ajout-versement input,
    #form-ajout-versement select,
    #form-ajout-versement button{

        width:100%;

    }



    /* HEADER SECTION VERSEMENT */

    .flex.items-center.justify-between.px-5.py-3\.5{

        flex-direction:column;

        align-items:flex-start;

        gap:10px;

    }


    #btn-toggle-ajout{

        width:100%;

    }


}



/* ==========================
   PETITS TELEPHONES
========================== */


@media(max-width:420px){


    .rounded-2xl{

        border-radius:16px;

    }


    .text-\[18px\]{

        font-size:16px;

    }



    /* TABLE */

    table{

        min-width:650px;

    }



    /* MODIFICATION VERSEMENT */

    .grid.grid-cols-2.sm\:grid-cols-6{

        display:flex;

        flex-direction:column;

    }


    .grid.grid-cols-2.sm\:grid-cols-6 input,
    .grid.grid-cols-2.sm\:grid-cols-6 select{

        width:100%;

    }


    .grid.grid-cols-2.sm\:grid-cols-6 div{

        justify-content:flex-end;

    }


}

</style>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const modeLabels = {
    espece: 'Espèces', cheque: 'Chèque', virement: 'Virement',
    wave: 'Wave', orange_money: 'Orange Money', agent_mandate: 'Agent mandaté',
};

function afficherAlerte(message, type = 'error') {
    const couleurs = type === 'error'
        ? 'background:#FCEBEB; color:#A32D2D; border-color:#F5C0C0'
        : 'background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0';

    const zone = document.getElementById('alerte-zone');
    zone.innerHTML = `<div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border" style="${couleurs}">${message}</div>`;
    setTimeout(() => { zone.innerHTML = ''; }, 4000);
}

function majTotaux(totaux) {
    document.getElementById('total-factures').textContent = Number(totaux.total_factures).toLocaleString('fr-FR') + ' F';
    document.getElementById('total-verse').textContent = Number(totaux.total_verse).toLocaleString('fr-FR') + ' F';

    const resteEl = document.getElementById('reste-a-payer');
    const badge = document.getElementById('badge-statut');

    if (totaux.solde) {
        resteEl.textContent = 'Soldé';
        resteEl.style.color = '#3B6D11';
        badge.textContent = 'SOLDÉ';
        badge.style.background = '#EAF3DE';
        badge.style.color = '#3B6D11';
    } else {
        resteEl.textContent = Number(totaux.reste_a_payer).toLocaleString('fr-FR') + ' F';
        resteEl.style.color = totaux.reste_a_payer > 0.01 ? '#A32D2D' : '#3B6D11';
        badge.textContent = 'Relevé de compte';
        badge.style.background = '#FEF6E4';
        badge.style.color = '#854F0B';
    }
}

function ligneNormale(p) {
    return `
        <td class="py-2.5 px-4 text-[12px] text-slate-600">${p.date}</td>

        <td class="py-2.5 px-4 text-right font-semibold text-[12px]" style="color:#3B6D11">
            ${Number(p.montant).toLocaleString('fr-FR')} F
        </td>

        <td class="py-2.5 px-4 text-[12px] text-slate-500">
            ${p.mode_label}
        </td>

        <td class="py-2.5 px-4 text-right whitespace-nowrap">
            <button onclick="modifierVersement(${p.id})"
                    class="text-blue-400 hover:text-blue-600 text-[20px] mr-2">
                ✎
            </button>

            <button onclick="supprimerVersement(${p.id})"
                    class="text-red-400 hover:text-red-600 text-[20px]">
                ×
            </button>
        </td>
    `;
}

function majDataset(tr, p) {
    tr.dataset.date = p.date_iso;
    tr.dataset.montant = p.montant;
    tr.dataset.mode = p.mode_paiement;
    tr.dataset.reference = p.reference || '';
}

document.getElementById('form-ajout-versement').addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.target;
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;

    try {
        const response = await fetch("{{ route('programmes.releve.paiements.store', $programme) }}", {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: new FormData(form),
        });

        const json = await response.json();

        if (!response.ok || !json.success) {
            const message = json.errors?.montant?.[0] || json.message || "Impossible d'enregistrer le versement.";
            afficherAlerte(message);
            return;
        }

        const ligneVide = document.getElementById('ligne-vide');
        if (ligneVide) ligneVide.remove();

        const tbody = document.getElementById('corps-versements');
        tbody.insertAdjacentHTML('afterbegin', `<tr id="versement-${json.paiement.id}"></tr>`);
        const tr = document.getElementById('versement-' + json.paiement.id);
        majDataset(tr, json.paiement);
        tr.innerHTML = ligneNormale(json.paiement);

        majTotaux(json.totaux);
        form.querySelector('select[name="mode_paiement"]').value="espece";
        form.querySelector('input[name="date"]').value = "{{ now()->format('Y-m-d') }}";
        afficherAlerte('Versement enregistré.', 'success');

    } catch (error) {
        console.error(error);
        afficherAlerte("Impossible d'enregistrer le versement.");
    } finally {
        button.disabled = false;
    }
});

function modifierVersement(id) {
    const tr = document.getElementById('versement-' + id);
    const d = tr.dataset;

    tr.innerHTML = `
        <td colspan="5" class="py-2.5 px-4">
            <div class="grid grid-cols-2 sm:grid-cols-6 gap-2 items-center bg-slate-50 rounded-xl p-3 border border-slate-100">
                <input id="date-${id}" type="date" value="${d.date}"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <input id="montant-${id}" type="number" step="0.01" value="${d.montant}" placeholder="Montant"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <select id="mode-${id}" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] bg-white focus:outline-none focus:border-blue-400">
                    ${Object.entries(modeLabels).map(([val, label]) =>
                        `<option value="${val}" ${d.mode === val ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
                <input id="reference-${id}" value="${d.reference || ''}" placeholder="Référence"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400 sm:col-span-2">
                <div class="flex items-center gap-1.5 justify-end">
                    <button type="button" onclick="annulerModifVersement(${id})"
                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 border border-slate-200 hover:bg-white" title="Annuler">&times;</button>
                    <button type="button" onclick="enregistrerVersement(${id})"
                            class="h-8 px-3 rounded-lg text-[11px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">✓</button>
                </div>
            </div>
        </td>
    `;
    document.getElementById(`montant-${id}`).focus();
}

function annulerModifVersement(id) {
    const tr = document.getElementById('versement-' + id);
    const d = tr.dataset;
    tr.innerHTML = ligneNormale({
        id, date: new Date(d.date + 'T00:00:00').toLocaleDateString('fr-FR'),
        montant: d.montant, mode_label: modeLabels[d.mode] || d.mode, reference: d.reference,
    });
}

async function enregistrerVersement(id) {
    const data = {
        date: document.getElementById(`date-${id}`).value,
        montant: document.getElementById(`montant-${id}`).value,
        mode_paiement: document.getElementById(`mode-${id}`).value,
        reference: document.getElementById(`reference-${id}`).value,
    };

    try {
        const response = await fetch(`/paiements/${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data),
        });

        const json = await response.json();

        if (!response.ok || !json.success) {
            const message = json.errors?.montant?.[0] || json.message || "Impossible d'enregistrer la modification.";
            afficherAlerte(message);
            return;
        }

        const tr = document.getElementById('versement-' + id);
        majDataset(tr, json.paiement);
        tr.innerHTML = ligneNormale(json.paiement);
        majTotaux(json.totaux);
        afficherAlerte('Versement modifié.', 'success');

    } catch (error) {
        console.error(error);
        afficherAlerte("Impossible d'enregistrer la modification.");
    }
}

async function supprimerVersement(id) {
    if (!confirm('Supprimer ce versement ?')) return;

    try {
        const response = await fetch(`/paiements/${id}`, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });

        const json = await response.json();
        if (!response.ok || !json.success) throw new Error();

        document.getElementById('versement-' + id)?.remove();
        majTotaux(json.totaux);

        const tbody = document.getElementById('corps-versements');
        if (!tbody.querySelector('tr')) {
            tbody.innerHTML = '<tr id="ligne-vide"><td colspan="5" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucun versement enregistré</td></tr>';
        }

        afficherAlerte('Versement supprimé.', 'success');

    } catch (error) {
        console.error(error);
        afficherAlerte('Impossible de supprimer ce versement.');
    }
}
</script>
</x-app-layout>