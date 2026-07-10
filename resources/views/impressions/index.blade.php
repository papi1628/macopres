<x-app-layout>
<x-slot name="title">Impression des fiches</x-slot>

<div x-data="impressionApp()" class="space-y-5">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-[15px] font-bold text-slate-800">Impression des fiches de présence</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Sélectionnez les employés, la période et le format</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('impressions.apercu') }}" target="_blank" id="formImpression">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ══════════════════════════════════════
             COL 1 — PÉRIODE
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">Période</h3>
            </div>
            <div class="px-5 py-4 space-y-4">

                {{-- Type de période --}}
                <div class="grid grid-cols-3 gap-2">
                    @foreach(['semaine' => 'Semaine', 'mois' => 'Mois', 'annee' => 'Année'] as $val => $label)
                        <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border cursor-pointer transition-all"
                               :class="periode === '{{ $val }}'
                                   ? 'border-blue-400 bg-blue-50'
                                   : 'border-slate-200 hover:border-slate-300'">
                            <input type="radio" name="periode" value="{{ $val }}"
                                   x-model="periode" class="sr-only">
                            <span class="text-[12px] font-semibold"
                                  :style="periode === '{{ $val }}' ? 'color:#185FA5' : 'color:#64748B'">
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Sélecteur mois --}}
                <div x-show="periode === 'mois'">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mois</label>
                    <input type="month" name="mois" x-model="moisSelectionne"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                </div>

                {{-- Sélecteur année --}}
                <div x-show="periode === 'annee'">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Année</label>
                    <select name="annee" x-model="anneeSelectionnee"
                            class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white">
                        @foreach(range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Info semaine --}}
                <div x-show="periode === 'semaine'"
                     class="px-3 py-2 rounded-xl text-[11px]" style="background:#EFF6FF; color:#185FA5">
                    Semaine en cours : {{ now()->startOfWeek()->locale('fr')->isoFormat('D MMM') }}
                    au {{ now()->endOfWeek()->locale('fr')->isoFormat('D MMM YYYY') }}
                </div>

            </div>
        </div>

        {{-- ══════════════════════════════════════
             COL 2 — FILTRES EMPLOYÉS
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">Filtrer par département</h3>
            </div>
            <div class="px-5 py-4 space-y-3">

                {{-- Tous --}}
                <button type="button" @click="filtrerDepartement('')"
                        class="w-full text-left px-3 py-2 rounded-xl text-[12px] font-semibold transition-colors"
                        :class="deptFiltre === '' ? 'text-white' : 'text-slate-600 hover:bg-slate-50'"
                        :style="deptFiltre === '' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                    Tous les départements
                    <span class="text-[10px] ml-1 opacity-70">({{ $employes->count() }})</span>
                </button>

                @foreach($departements as $dept)
                    <button type="button" @click="filtrerDepartement('{{ $dept }}')"
                            class="w-full text-left px-3 py-2 rounded-xl text-[12px] font-semibold transition-colors capitalize"
                            :class="deptFiltre === '{{ $dept }}' ? 'text-white' : 'text-slate-600 hover:bg-slate-50'"
                            :style="deptFiltre === '{{ $dept }}' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                        {{ $dept }}
                        <span class="text-[10px] ml-1 opacity-70">
                            ({{ $employes->where('departement', $dept)->count() }})
                        </span>
                    </button>
                @endforeach

            </div>
        </div>

        {{-- ══════════════════════════════════════
             COL 3 — SÉLECTION EMPLOYÉS
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                <h3 class="text-[12px] font-semibold text-slate-800">
                    Employés
                    <span class="ml-1 text-[10px] font-normal text-slate-400">
                        (<span x-text="selectionnes.length"></span> sélectionné(s))
                    </span>
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" @click="toutSelectionner()"
                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition-colors"
                            style="color:#185FA5; background:#E6F1FB">
                        Tout
                    </button>
                    <button type="button" @click="toutDeselectionner()"
                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition-colors"
                            style="color:#A32D2D; background:#FCEBEB">
                        Aucun
                    </button>
                </div>
            </div>

            {{-- Recherche --}}
            <div class="px-4 pt-3">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input x-model="recherche" type="text" placeholder="       Rechercher…"
                           class="w-full pl-8 pr-3 h-8 border border-slate-200 rounded-xl text-[12px] focus:outline-none focus:border-blue-400">
                </div>
            </div>

            {{-- Liste --}}
            <div class="px-4 py-3 max-h-64 overflow-y-auto space-y-1">
                @foreach($employes as $employe)
                    <label class="flex items-center gap-3 px-3 py-2 rounded-xl cursor-pointer transition-colors hover:bg-slate-50"
                           x-show="filtreEmploye({{ $employe->id }}, '{{ $employe->departement }}', '{{ strtolower($employe->prenom . ' ' . $employe->nom) }}')"
                           :class="selectionnes.includes({{ $employe->id }}) ? 'bg-blue-50' : ''">
                        <input type="checkbox"
                               name="employe_ids[]"
                               value="{{ $employe->id }}"
                               x-model="selectionnes"
                               :value="{{ $employe->id }}"
                               class="w-4 h-4 rounded cursor-pointer flex-shrink-0"
                               style="accent-color:#185FA5">
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[8px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                            {{ mb_strtoupper(substr($employe->prenom,0,1).substr($employe->nom,0,1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-semibold text-slate-800 truncate">{{ $employe->prenom }} {{ $employe->nom }}</p>
                            <p class="text-[10px] text-slate-400">{{ $employe->departement ?? '–' }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════
         BOUTONS D'ACTION
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Résumé sélection --}}
            <div class="flex-1 min-w-[200px]">
                <p class="text-[12px] text-slate-600">
                    <span class="font-bold" x-text="selectionnes.length"></span> employé(s) sélectionné(s)
                    <span x-show="selectionnes.length === 0" class="text-red-400 ml-1">— Sélectionnez au moins un employé</span>
                </p>
            </div>

            {{-- Aperçu + Impression --}}
            <button type="submit"
                    :disabled="selectionnes.length === 0"
                    :class="selectionnes.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-px'"
                    class="flex items-center gap-2 h-10 px-5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                Aperçu & Imprimer
            </button>

        </div>
    </div>

    </form>

</div>

@php
$employesData = $employes->map(function ($e) {
    return [
        'id' => $e->id,
        'departement' => $e->departement ?? '',
        'nom' => strtolower($e->prenom.' '.$e->nom),
    ];
})->values();
@endphp

<script>
    
const EMPLOYES_DATA = @json($employesData);

function impressionApp() {
    return {
        periode: 'mois',
        moisSelectionne: '{{ now()->format('Y-m') }}',
        anneeSelectionnee: {{ now()->year }},
        deptFiltre: '',
        recherche: '',
        selectionnes: EMPLOYES_DATA.map(e => e.id), // tous sélectionnés par défaut

        filtrerDepartement(dept) {
            this.deptFiltre = dept;
            if (dept === '') {
                this.selectionnes = EMPLOYES_DATA.map(e => e.id);
            } else {
                this.selectionnes = EMPLOYES_DATA
                    .filter(e => e.departement === dept)
                    .map(e => e.id);
            }
        },

        filtreEmploye(id, dept, nom) {
            const deptOk = this.deptFiltre === '' || dept === this.deptFiltre;
            const rechercheOk = this.recherche === '' || nom.includes(this.recherche.toLowerCase());
            return deptOk && rechercheOk;
        },

        toutSelectionner() {
            this.selectionnes = EMPLOYES_DATA
                .filter(e => this.deptFiltre === '' || e.departement === this.deptFiltre)
                .map(e => e.id);
        },

        toutDeselectionner() {
            this.selectionnes = [];
        },
    };
}
</script>
</x-app-layout>