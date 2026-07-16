<x-app-layout>
<x-slot name="title">Bons de commande — {{ $programme->ecole->nom }}</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
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
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#DBEAFE; color:#1D4ED8">Bons de commande</span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $programme->annee_scolaire }}</p>
            </div>
            <a href="{{ route('programmes.show', $programme) }}"
               class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                ← Retour au programme
            </a>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <h3 class="text-[13px] font-bold text-slate-800">Commandes ({{ $programme->bonsCommande->count() }})</h3>
        <form method="POST" action="{{ route('programmes.bons.store', $programme) }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                    style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nouvelle commande
            </button>
        </form>
    </div>

    @if($programme->bonsCommande->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-[14px] font-semibold text-slate-600">Aucune commande enregistrée</p>
            <p class="text-[12px] text-slate-400 mt-1">Le client n'a pas encore passé de commande.</p>
        </div>
    @else
        {{-- Sous-timeline des BC (ancien accordéon, inchangé) --}}
        <div class="relative pl-5 sm:pl-8">
            <div class="absolute left-[15px] top-2 bottom-2 w-px" style="background:#E2E8F0"></div>

            @foreach($programme->bonsCommande as $i => $bon)
                <div x-data="{ open: {{ session('bon_ouvert') == $bon->id ? 'true' : 'false' }} }" class="relative mb-5">
                    <div class="absolute -left-5 sm:-left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black text-white"
                         style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                        {{ $i + 1 }}
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 sm:px-5 py-4 cursor-pointer" @click="open = !open">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400">Commande n°{{ $i + 1 }}</p>
                                <p class="font-mono font-bold text-[14px]" style="color:#0C447C">{{ $bon->numero }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 sm:gap-6 text-left sm:text-right w-full sm:w-auto">
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Date</p>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $bon->date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Montant</p>
                                    <p class="text-[13px] font-bold" style="color:#185FA5">{{ number_format($bon->montant, 0, ',', ' ') }} F</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Articles</p>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $bon->lignes->count() }}</p>
                                </div>
                            </div>
                            {{-- <a href="{{ route('programmes.bons.facture', $bon) }}" target="_blank" @click.stop
                            class="h-8 px-3 rounded-lg text-[11px] font-semibold text-white flex items-center gap-1.5 flex-shrink-0"
                            style="background:linear-gradient(135deg,#3B6D11,#5A9A1E)">
                                Facture
                            </a> --}}

                            {{-- Le bouton doit occuper les 1/3 de l'espace --}}
                            <a href="{{ route('programmes.bons.imprimer', $bon) }}" target="_blank" @click.stop
                            class="sm:ml-4 h-8 px-3 rounded-lg text-[11px] font-semibold text-white flex items-center gap-1.5 flex-shrink-0 w-full sm:w-auto justify-center"
                            style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                                Imprimer
                            </a>

                            
                            
                        </div>

                        <div x-show="open" class="border-t border-slate-100">
                            <div class="px-5 py-3 grid grid-cols-2 sm:grid-cols-3 gap-4" style="background:#f8fafc">
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase mb-1">Nature</p>
                                    <p class="text-[12px] text-slate-700">{{ $bon->nature ?? '–' }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase mb-1">Condition de paiement</p>
                                    <select
                                        onchange="updateCondition(this, '{{ route('programmes.bons.condition', $bon) }}')"
                                        class="h-7 border border-slate-200 rounded-lg px-2 py-1 text-[11px] bg-white text-slate-700">

                                        <option value="" {{ !$bon->condition_paiement ? 'selected' : '' }}>–</option>

                                        @foreach(\App\Models\BonCommande::conditionsProposees() as $cp)
                                            <option value="{{ $cp }}"
                                                {{ $bon->condition_paiement === $cp ? 'selected' : '' }}>
                                                {{ $cp }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                @if($bon->montant > 0)
                                <div class="col-span-2 sm:col-span-1">
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase mb-1">Montant en lettres</p>
                                    <p class="text-[11px] italic text-slate-500">{{ \App\Support\NombreEnLettres::enMontant($bon->montant) }}</p>
                                </div>
                                @endif
                            </div>

                            <div class="px-5 py-4">
                                <p class="text-[11px] font-bold text-slate-700 mb-2">Articles</p>

                                @if($bon->lignes->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-[850px] w-full text-[12px]">
                                            <thead>
                                                <tr class="border-b border-slate-100">
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Désignation</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Taille / Classe</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Couleur</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Matière</th>
                                                    <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Logo</th>
                                                    <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Quantité</th>
                                                    <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">P.U</th>
                                                    <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="lignes-bon-{{ $bon->id }}" class="divide-y divide-slate-50">
                                                @foreach($bon->lignes as $ligne)
                                                    <tr
                                                        id="ligne-{{ $ligne->id }}"
                                                        data-bon-id="{{ $bon->id }}"   {{-- ← à ajouter --}}
                                                        data-designation="{{ $ligne->designation_libre }}"
                                                        data-taille="{{ $ligne->taille }}"
                                                        data-couleur="{{ $ligne->couleur }}"
                                                        data-matiere="{{ $ligne->matiere }}"
                                                        data-quantite="{{ $ligne->quantite }}"
                                                        data-prix="{{ $ligne->prix_unitaire }}"
                                                        data-logo="{{ $ligne->logo }}">

                                                        <td class="py-2 font-semibold text-slate-700">{{ $ligne->libelle() }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->taille ?? '–' }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->couleur ?? '–' }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->matiere ?? '–' }}</td>
                                                        <td class="py-2 text-center">{{ $ligne->logo ? '✓' : '–' }}</td>
                                                        <td class="py-2 text-center font-semibold">{{ $ligne->quantite }}</td>
                                                        <td class="py-2 text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</td>
                                                        <td class="py-2 text-right font-semibold" style="color:#185FA5">{{ number_format($ligne->montant_ligne, 0, ',', ' ') }}</td>
                                                        <td class="py-2 text-right">

                                                            <button
                                                                type="button"
                                                                onclick="modifierArticle({{ $ligne->id }})"
                                                                class="text-blue-400 hover:text-blue-600 text-[20px] mr-2">
                                                                ✎
                                                            </button>


                                                            <button
                                                                type="button"
                                                                onclick="supprimerArticle(
                                                                    this,
                                                                    '{{ route('programmes.bons.lignes.destroy', $ligne) }}',
                                                                    {{ $bon->id }},
                                                                    {{ $ligne->id }}
                                                                )"
                                                                class="text-red-400 hover:text-red-600 text-[20px]">
                                                                &times;
                                                            </button>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background:#f8fafc">
                                                    <td colspan="7" class="py-2 text-[11px] font-bold text-slate-600 text-right">TOTAL</td>
                                                    <td
                                                        id="montant-bon-{{ $bon->id }}"
                                                        class="py-2 text-right font-bold text-[12px]"
                                                        style="color:#0C447C">{{ number_format($bon->montant, 0, ',', ' ') }} F</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-[12px] text-slate-400 text-center py-4">0 article — ajoutez-en un ci-dessous</p>
                                @endif

                                <div x-data="{ openAjout: {{ $bon->lignes->count() === 0 ? 'true' : 'false' }}, ...ligneForm() }" class="mt-3">
                                    
                                    <button
                                        type="button"
                                        @click="openAjout = !openAjout"
                                        class="text-[11px] font-semibold"
                                        style="color:#185FA5">
                                        <span x-text="openAjout ? '– Fermer' : '+ Ajouter un article'"></span>
                                    </button>

                                    <form id="form-bon-{{ $bon->id }}" method="POST" x-show="openAjout" onsubmit="ajouterArticle(event,this,{{ $bon->id }})"
                                            action="{{ route('programmes.bons.lignes.store',$bon) }}"
                                          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 mt-3">
                                        @csrf
                                        <input type="text" name="designation_libre" x-show="!designationId" placeholder="Nom de l'article"
                                               class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] sm:col-span-2">
                                        <input type="text" name="taille" placeholder="Taille (12, M, XL...) / Classe (CI, 6e, Terminale...)" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="number" name="quantite" placeholder="Quantité" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="text" name="couleur" placeholder="Couleur" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="text" name="matiere" placeholder="Matière" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="number" step="0.01" name="prix_unitaire" x-model="prixUnitaire" placeholder="Prix unitaire" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                            <input type="checkbox" name="logo" value="1" class="w-3.5 h-3.5" style="accent-color:#185FA5"> Avec logo
                                        </label>
                                        <button type="submit" class="sm:col-span-4 h-8 rounded-lg text-[11px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter l'article</button>
                                    </form>
                                </div>
                            </div>

                            <div class="px-5 pb-4">
                                <form method="POST" action="{{ route('programmes.bons.destroy', $bon) }}" onsubmit="return confirm('Supprimer cette commande et tous ses articles ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] font-semibold text-red-400 hover:text-red-600">Supprimer cette commande</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>

const urlSuppressionLigne = "{{ url('/lignes-bon-commande') }}";

function ligneForm() {
    return {
        designationId: '',
        prixUnitaire: '',
        appliquerPrix(event) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            this.prixUnitaire = option?.dataset?.prix ?? '';
        },
    };
}

/* ──────────────────────────────────────────
   Rendu HTML des cellules d'une ligne (mode normal)
   Réutilisé après ajout, modification et annulation.
────────────────────────────────────────── */
function celulasLigne(ligne) {
    return `
        <td class="py-2 font-semibold text-slate-700">${ligne.designation}</td>
        <td class="py-2 text-slate-500">${ligne.taille || '–'}</td>
        <td class="py-2 text-slate-500">${ligne.couleur || '–'}</td>
        <td class="py-2 text-slate-500">${ligne.matiere || '–'}</td>
        <td class="py-2 text-center">${ligne.logo ? '✓' : '–'}</td>
        <td class="py-2 text-center font-semibold">${ligne.quantite}</td>
        <td class="py-2 text-right">${Number(ligne.prix_unitaire).toLocaleString('fr-FR')}</td>
        <td class="py-2 text-right font-semibold" style="color:#185FA5">${Number(ligne.montant_ligne).toLocaleString('fr-FR')}</td>
        <td class="py-2 text-right whitespace-nowrap sticky right-0 bg-white">
            <button type="button" onclick="modifierArticle(${ligne.id})"
                    class="text-blue-400 hover:text-blue-600 text-[20px] mr-2 align-middle" title="Modifier">✎</button>
            <button type="button" onclick="supprimerArticle(this, '${urlSuppressionLigne}/${ligne.id}', ${ligne.bon_commande_id}, ${ligne.id})"
                    class="text-red-400 hover:text-red-600 text-[20px] align-middle" title="Supprimer">&times;</button>
        </td>
    `;
}

function majDatasetLigne(tr, ligne) {
    tr.dataset.designation = ligne.designation_libre ?? ligne.designation ?? '';
    tr.dataset.taille      = ligne.taille ?? '';
    tr.dataset.couleur     = ligne.couleur ?? '';
    tr.dataset.matiere     = ligne.matiere ?? '';
    tr.dataset.quantite    = ligne.quantite;
    tr.dataset.prix        = ligne.prix_unitaire;
    tr.dataset.logo        = ligne.logo ? 1 : 0;
    tr.dataset.bonId       = ligne.bon_commande_id;
}

/* ──────────────────────────────────────────
   Ajouter un article
────────────────────────────────────────── */
async function ajouterArticle(event, form, bonId) {
    event.preventDefault();

    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    button.textContent = "Ajout...";

    try {
        const data = new FormData(form);

        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: data,
        });

        const json = await response.json();
        if (!response.ok || !json.success) throw new Error("Erreur ajout article");

        const tbody = document.getElementById("lignes-bon-" + bonId);
        tbody.insertAdjacentHTML("beforeend", `<tr id="ligne-${json.ligne.id}"></tr>`);
        const tr = document.getElementById("ligne-" + json.ligne.id);
        majDatasetLigne(tr, json.ligne);
        tr.innerHTML = celulasLigne(json.ligne);

        document.getElementById("montant-bon-" + bonId).innerHTML =
            Number(json.montant).toLocaleString('fr-FR') + " F";

        form.reset();
        const premierChamp = form.querySelector('input:not([type="hidden"])');
        if (premierChamp) premierChamp.focus();

    } catch (error) {
        console.error(error);
        alert("Impossible d'ajouter l'article.");
    } finally {
        button.disabled = false;
        button.textContent = "Ajouter l'article";
    }
}

/* ──────────────────────────────────────────
   Condition de paiement
────────────────────────────────────────── */
async function updateCondition(select, url) {

    try {
        const response = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ condition_paiement: select.value }),
        });
        if (!response.ok) throw new Error('Erreur');
    } catch (e) {
        alert("Impossible d'enregistrer.");
    }
}

/* ──────────────────────────────────────────
   Supprimer un article
────────────────────────────────────────── */
async function supprimerArticle(button, url, bonId, ligneId) {
    if (!confirm("Supprimer cet article ?")) return;

    button.disabled = true;

    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        if (!response.ok) throw new Error("HTTP " + response.status);
        const json = await response.json();
        if (!json.success) throw new Error();

        const ligne = document.getElementById("ligne-" + ligneId);
        if (ligne) ligne.remove();

        document.getElementById("montant-bon-" + bonId).innerHTML =
            Number(json.montant).toLocaleString('fr-FR') + " F";

    } catch (error) {
        console.error(error);
        alert("Impossible de supprimer l'article.");
        button.disabled = false;
    }
}

/* ──────────────────────────────────────────
   Modifier un article — ligne d'édition stylée
────────────────────────────────────────── */
function modifierArticle(id) {
    const tr = document.getElementById("ligne-" + id);
    const d  = tr.dataset;

    tr.innerHTML = `
        <td colspan="9" class="py-2.5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-8 gap-2 items-center bg-slate-50 rounded-xl p-3 border border-slate-100">
                <input id="designation-${id}" value="${d.designation ?? ''}" placeholder="Désignation"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400 sm:col-span-2">
                <input id="taille-${id}" value="${d.taille ?? ''}" placeholder="Taille"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <input id="couleur-${id}" value="${d.couleur ?? ''}" placeholder="Couleur"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <input id="matiere-${id}" value="${d.matiere ?? ''}" placeholder="Matière"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <input id="quantite-${id}" type="number" value="${d.quantite ?? ''}" placeholder="Qté"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">
                <input id="prix-${id}" type="number" step="0.01" value="${d.prix ?? ''}" placeholder="Prix unitaire"
                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] focus:outline-none focus:border-blue-400">

                <label class="flex items-center gap-1.5 text-[11px] text-slate-500 whitespace-nowrap">
                    <input id="logo-${id}" type="checkbox" ${Number(d.logo) === 1 ? 'checked' : ''}
                           class="w-3.5 h-3.5" style="accent-color:#185FA5"> Avec logo
                </label>

                <div class="flex items-center gap-1.5 justify-end sm:col-span-1">
                    <button type="button" onclick="annulerModification(${id})"
                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-400 border border-slate-200 hover:bg-white hover:text-slate-600 transition-colors"
                            title="Annuler">
                        &times;
                    </button>
                    <button type="button" onclick="enregistrerArticle(${id})"
                            class="h-8 px-3 flex items-center justify-center rounded-lg text-[11px] font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                            title="Enregistrer">
                        ✓ Enregistrer
                    </button>
                </div>
            </div>
        </td>
    `;

    document.getElementById(`designation-${id}`).focus();
}

function annulerModification(id) {
    const tr = document.getElementById("ligne-" + id);
    const d  = tr.dataset;

    tr.innerHTML = celulasLigne({
        id,
        designation:     d.designation || '–',
        designation_libre: d.designation,
        taille:          d.taille,
        couleur:         d.couleur,
        matiere:         d.matiere,
        quantite:        d.quantite,
        prix_unitaire:   d.prix,
        montant_ligne:   d.quantite * d.prix,
        logo:            Number(d.logo) === 1,
        bon_commande_id: d.bonId,
    });
}

async function enregistrerArticle(id) {
    const data = {
        designation_libre: document.getElementById('designation-' + id).value,
        taille:            document.getElementById('taille-' + id).value,
        couleur:           document.getElementById('couleur-' + id).value,
        matiere:           document.getElementById('matiere-' + id).value,
        quantite:          document.getElementById('quantite-' + id).value,
        prix_unitaire:     document.getElementById('prix-' + id).value,
        logo:              document.getElementById('logo-' + id).checked ? 1 : 0,
    };

    try {
        const response = await fetch(`/lignes-bon-commande/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        });

        const json = await response.json();
        if (!response.ok || !json.success) throw new Error('Erreur');

        const tr = document.getElementById("ligne-" + id);
        majDatasetLigne(tr, json.ligne);
        tr.innerHTML = celulasLigne(json.ligne);

        document.getElementById("montant-bon-" + json.ligne.bon_commande_id).innerHTML =
            Number(json.montant).toLocaleString('fr-FR') + " F";

    } catch (error) {
        console.error(error);
        alert("Impossible d'enregistrer la modification.");
    }
}
</script>
</x-app-layout>