<x-app-layout>
<x-slot name="title">Nouveau programme</x-slot>

<div x-data="programmeForm()" class="space-y-5 max-w-4xl">

    <form method="POST" action="{{ route('programmes.store') }}">
        @csrf

        {{-- ══════════════════════════════════════
             ÉCOLE
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">École / Client</h3>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex gap-2">
                    <button type="button" @click="ecoleMode = 'existante'"
                            class="px-4 py-2 rounded-xl text-[12px] font-semibold transition-colors"
                            :class="ecoleMode === 'existante' ? 'text-white' : 'text-slate-500 border border-slate-200'"
                            :style="ecoleMode === 'existante' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                        École existante
                    </button>
                    <button type="button" @click="ecoleMode = 'nouvelle'"
                            class="px-4 py-2 rounded-xl text-[12px] font-semibold transition-colors"
                            :class="ecoleMode === 'nouvelle' ? 'text-white' : 'text-slate-500 border border-slate-200'"
                            :style="ecoleMode === 'nouvelle' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                        Nouvelle école
                    </button>
                </div>

                <div x-show="ecoleMode === 'existante'">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Sélectionner l'école *</label>
                    <select name="ecole_id" x-bind:required="ecoleMode === 'existante'"
                            class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white text-slate-700">
                        <option value="">-- Choisir --</option>
                        @foreach($ecoles as $ecole)
                            <option value="{{ $ecole->id }}">{{ $ecole->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="ecoleMode === 'nouvelle'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nom de l'école *</label>
                        <input type="text" name="ecole_nom" x-bind:required="ecoleMode === 'nouvelle'"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Adresse</label>
                        <input type="text" name="ecole_adresse"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Téléphone</label>
                        <input type="text" name="ecole_telephone"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Responsable (nom)</label>
                        <input type="text" name="ecole_contact_nom"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Téléphone du responsable</label>
                        <input type="text" name="ecole_contact_telephone"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>

                <div class="max-w-xs">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Année scolaire *</label>
                    <input type="text" name="annee_scolaire" placeholder="2025/2026" required
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             CONTRAT
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">Contrat de prestation</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Description de l'engagement (produits / quantités)</label>
                    <textarea name="description_engagement" rows="3" placeholder="Ex : 600 uniformes composées de pantalon..., 600 chemises..., etc."
                              class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-400"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Montant total du contrat (FCFA)</label>
                        <input type="number" step="0.01" name="montant_total"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Représentant de l'école</label>
                        <input type="text" name="representant_client"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date limite de livraison</label>
                        <input type="date" name="date_limite_livraison"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Délai de livraison (texte)</label>
                        <input type="text" name="delai_livraison_texte" placeholder="Ex : avant la rentrée scolaire"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date de signature</label>
                        <input type="date" name="date_signature"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-5 px-4 py-3 rounded-xl text-[11px]" style="background:#EFF6FF; color:#185FA5">
            Les bons de commande (avec leurs articles : désignation, taille, couleur, quantité...) s'ajoutent juste après, sur la fiche du programme.
        </div>

        {{-- ══════════════════════════════════════
             ÉCHÉANCES DE PAIEMENT
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">Échéancier de paiement</h3>
                <button type="button" @click="echeances.push({date_prevue:'', montant_prevu:''})"
                        class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                    + Ajouter une échéance
                </button>
            </div>
            <div class="p-5 space-y-3">
                <template x-for="(ech, i) in echeances" :key="i">
                    <div class="grid grid-cols-12 gap-2 items-end border border-slate-100 rounded-xl p-3">
                        <div class="col-span-1 text-[11px] font-bold text-slate-400 flex items-center h-8">
                            <span x-text="'#' + (i+1)"></span>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-[9px] font-semibold text-slate-400 uppercase mb-1">Date prévue</label>
                            <input type="date" :name="`echeances[${i}][date_prevue]`" x-model="ech.date_prevue" required
                                   class="w-full h-8 border border-slate-200 rounded-lg px-2 text-[12px]">
                        </div>
                        <div class="col-span-5">
                            <label class="block text-[9px] font-semibold text-slate-400 uppercase mb-1">Montant prévu</label>
                            <input type="number" step="0.01" :name="`echeances[${i}][montant_prevu]`" x-model="ech.montant_prevu" required
                                   class="w-full h-8 border border-slate-200 rounded-lg px-2 text-[12px]">
                        </div>
                        <div class="col-span-1 text-right">
                            <button type="button" @click="echeances.splice(i,1)" class="text-red-400 hover:text-red-600 text-lg">&times;</button>
                        </div>
                    </div>
                </template>
                <p x-show="echeances.length === 0" class="text-[11px] text-slate-400 text-center py-4">Aucune échéance ajoutée</p>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('programmes.index') }}"
               class="h-10 px-5 rounded-xl text-sm font-semibold border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center">
                Annuler
            </a>
            <button type="submit"
                    class="h-10 px-6 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                Enregistrer le programme
            </button>
        </div>
    </form>
</div>

<script>
function programmeForm() {
    return {
        ecoleMode: 'existante',
        echeances: [],
    };
}
</script>
</x-app-layout>