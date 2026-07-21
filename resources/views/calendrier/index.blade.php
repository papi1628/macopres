<x-app-layout>
<x-slot name="title">Calendrier RH</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#FCEBEB; color:#A32D2D; border-color:#F5C0C0">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

<div x-data="calendrierApp()" class="space-y-4">

    {{-- ══════════════════════════════════════
         HEADER NAVIGATION
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4">
        <div class="flex items-center justify-between flex-wrap gap-3">

            {{-- Titre mois/année --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[16px] font-black text-slate-800 leading-tight capitalize">
                        <span x-text="titreMois"></span>
                    </h2>
                    <p class="text-[10px] text-slate-400">Calendrier RH — MACOPRES</p>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex items-center gap-2 flex-wrap">

                {{-- Sélecteur mois/année --}}
                <div class="flex items-center gap-2">
                    <select 
                        x-model="mois"
                        @change="changeMonth(mois, annee)"
                            class="h-9 border border-slate-200 rounded-xl px-3 pr-8 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ $m == $date->month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m)->locale('fr')->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select 
                        x-model="annee"
                        @change="changeMonth(mois, annee)"
                            class="h-9 border border-slate-200 rounded-xl px-3 pr-8 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
                        @foreach(range(now()->year - 2, now()->year + 2) as $y)
                            <option value="{{ $y }}" {{ $y == $date->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Flèches --}}
                <div class="flex items-center gap-1">
                    <button 
                    @click="
                    let d = new Date(annee, mois-2, 1);
                    changeMonth(d.getMonth()+1, d.getFullYear());"
                        class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button
                    @click="changeMonth({{ now()->month }}, {{ now()->year }})"
                    class="flex items-center h-9 px-3 rounded-xl text-[12px] font-semibold text-white"
                    style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        Aujourd'hui
                    </button>
                    <button 
                        @click="
                        let d = new Date(annee, mois, 1);
                        changeMonth(d.getMonth()+1, d.getFullYear());
"
                        class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                {{-- Bouton ajouter --}}
                @if(Auth::user()->role === 'directeur')
                <button @click="openCreate('{{ now()->format('Y-m-d') }}')"
                        class="flex items-center gap-2 h-9 px-4 rounded-xl text-[12px] font-semibold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Ajouter un événement
                </button>
                @endif
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex flex-wrap gap-4 mt-4 pt-3 border-t border-slate-100">
            @foreach([
                ['Férié payé', '#DBEAFE', '#1D4ED8'],
                /*['Repos', '#F3F4F6', '#374151'],
                ['Événement', '#DCFCE7', '#166534'],*/
                ['Aujourd\'hui', '#EFF6FF', '#185FA5'],
                ['Week-end', '#F1F5F9', '#94A3B8'],
            ] as [$label, $bg, $color])
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-full" style="background:{{ $bg }}; border:1px solid {{ $color }}20"></div>
                    <span class="text-[10px] text-slate-500">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════
         GRILLE CALENDRIER
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- En-têtes jours --}}
        <div class="grid grid-cols-7 border-b border-slate-100">
            @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $i => $jourNom)
                <div class="py-3 text-center text-[10px] font-bold uppercase tracking-wider
                            {{ $i === 6 ? 'text-red-400' : 'text-slate-400' }}">
                    {{ $jourNom }}
                </div>
            @endforeach
        </div>

        {{-- Jours --}}
        <div class="grid grid-cols-7">

            <template x-for="(jour,index) in jours" :key="jour.date">

                <button
                type="button"
                @click="openDay(jour.date)"
                class="relative min-h-[90px] sm:min-h-[110px] p-2 sm:p-3 text-left border-r border-b border-slate-100"
                :class="{
                'opacity-30': !jour.dans_mois,
                'ring-2 ring-inset ring-blue-400': jour.aujourdhui
                }"
                :style="
                jour.aujourdhui 
                ? 'background:#EFF6FF'
                : jour.weekend 
                ? 'background:#F8FAFC'
                : 'background:white'
                "
                >


                    <div class="flex items-start justify-between mb-1">

                        <span 
                            class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[12px] font-bold"
                            :class="
                                jour.aujourdhui 
                                ? 'text-white' 
                                : (jour.dimanche ? 'text-red-400' : 'text-slate-700')
                            "
                            :style="
                                jour.aujourdhui 
                                ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' 
                                : ''
                            "
                            x-text="jour.jour">
                        </span>


                        <span 
                            x-show="jour.aujourdhui"
                            class="hidden sm:block text-[8px] font-bold px-1.5 py-0.5 rounded-full text-white"
                            style="background:#185FA5">
                            Aujourd'hui
                        </span>

                    </div>


                    <div class="space-y-1 mt-1">

                        <!-- événements -->
                        <template x-for="event in jour.evenements.slice(0,2)" :key="event.id">

                            <div class="text-[9px] sm:text-[10px] px-1.5 py-0.5 rounded-md font-semibold truncate"
                                :style="`
                                    background:${getBadge(event.type).bg};
                                    color:${getBadge(event.type).color}
                                `"
                                x-text="event.titre">
                            </div>

                        </template>


                        <!-- + autres -->
                        <template x-if="jour.evenements.length > 2">

                            <div class="text-[9px] text-slate-400 font-semibold">
                                +<span x-text="jour.evenements.length - 2"></span> autre(s)
                            </div>

                        </template>


                        <!-- dimanche vide -->
                        <template x-if="jour.dimanche && jour.evenements.length === 0">

                            <div class="text-[9px] px-1.5 py-0.5 rounded-md font-medium"
                                style="background:#F1F5F9; color:#94A3B8">
                                Week-end
                            </div>

                        </template>

                    </div>


                </button>

            </template>

        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL — JOUR CLIQUÉ
    ══════════════════════════════════════ --}}
    <div x-show="dayModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="dayModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto"
             @click.outside="dayModal = false">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800" x-text="selectedDateLabel"></h2>
                    <p class="text-[11px] text-slate-400 mt-0.5">Événements du jour</p>
                </div>
                <button @click="dayModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl">&times;</button>
            </div>

            <div class="px-6 py-4">
                {{-- Liste événements du jour --}}
                <div class="space-y-2 mb-5">
                    <template x-if="selectedEvents.length === 0">
                        <div class="text-center py-6 text-slate-400 text-[13px]">
                            Aucun événement ce jour
                        </div>
                    </template>
                    <template x-for="event in selectedEvents" :key="event.id">
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full flex-shrink-0"
                                     :style="`background:${getBadgeColor(event.type)}`"></div>
                                <div>
                                    <p class="text-[13px] font-semibold text-slate-800" x-text="event.titre"></p>
                                    <p class="text-[10px] text-slate-400" x-text="getTypeLabel(event.type) + (event.est_paye ? ' · Payé' : '')"></p>
                                </div>
                            </div>
                            @if(Auth::user()->role === 'directeur')
                            <div class="flex items-center gap-1">
                                <button @click="openEdit(event)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-amber-50 text-amber-500 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                                    </svg>
                                </button>
                                <button @click="confirmDelete(event)"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-red-50 text-red-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                            @endif
                        </div>
                    </template>
                </div>

                @if(Auth::user()->role === 'directeur')
                {{-- Bouton ajouter sur ce jour --}}
                <button @click="dayModal = false; openCreate(selectedDate)"
                        class="w-full h-10 rounded-xl text-[13px] font-semibold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    + Ajouter un événement ce jour
                </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL — CRÉER / MODIFIER ÉVÉNEMENT
    ══════════════════════════════════════ --}}
    @if(Auth::user()->role === 'directeur')
    <div x-show="createModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="createModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             @click.outside="createModal = false">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[15px] font-bold text-slate-800"
                    x-text="editMode ? 'Modifier l\'événement' : 'Nouvel événement'"></h2>
                <button @click="createModal = false"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl">&times;</button>
            </div>

            <form @submit.prevent="saveEvent"
                class="px-6 py-5 space-y-4">
                @csrf
                

                {{-- Date --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date *</label>
                    <input type="date" name="date" x-model="form.date" required
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Type *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="opt in typeOptions" :key="opt.value">
                            <label class="flex items-center gap-2 p-3 rounded-xl border cursor-pointer transition-all"
                                   :style="form.type === opt.value ? `background:${opt.bg}; border-color:${opt.color}` : 'background:white; border-color:#e2e8f0'">
                                <input type="radio" name="type" :value="opt.value" x-model="form.type" class="sr-only">
                                <div class="w-2 h-2 rounded-full" :style="`background:${opt.color}`"></div>
                                <span class="text-[12px] font-semibold" :style="`color:${opt.color}`" x-text="opt.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                {{-- Titre --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Titre *</label>
                    <input type="text" name="titre" x-model="form.titre" required
                           placeholder="Ex: Tabaski, Fête du travail…"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea name="description" x-model="form.description" rows="2"
                              class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 resize-none"></textarea>
                </div>

                {{-- Journée payée --}}
                <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#EFF6FF">
                    <input type="checkbox" name="est_paye" id="est_paye" value="1"
                           x-model="form.est_paye"
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color:#185FA5">
                    <label for="est_paye" class="text-[13px] font-semibold cursor-pointer" style="color:#185FA5">
                        Journée payée — les employés touchent leur salaire
                    </label>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="createModal = false"
                            class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        <span x-text="editMode ? 'Enregistrer' : 'Créer l\'événement'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL — CONFIRMATION SUPPRESSION
    ══════════════════════════════════════ --}}
    <div x-show="deleteModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background:#FCEBEB">
                    <svg class="w-5 h-5" style="color:#A32D2D" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Supprimer l'événement</h3>
                    <p class="text-sm text-slate-500 mt-0.5" x-text="'« ' + deleteEvent?.titre + ' »'"></p>
                </div>
            </div>
            <p class="text-xs text-slate-400 mb-5">Cette action est irréversible. L'événement sera supprimé du calendrier.</p>
            <div class="flex gap-3">
                <button @click="deleteModal = false"
                        class="flex-1 h-9 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50">
                    Annuler
                </button>
                <div class="flex-1">
                    <button 
                    @click="deleteEventAjax()"
                    class="w-full h-9 rounded-xl text-sm font-bold text-white"
                    style="background:#A32D2D">

                    Supprimer

                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
function calendrierApp() {
    return {
        dayModal: false,
        createModal: false,
        events: [],
        deleteModal: false,
        editMode: false,
        selectedDate: '',
        selectedDateLabel: '',
        selectedEvents: [],
        deleteEvent: null,
        jours: @json($jours),
        mois: {{ $date->month }},
        annee: {{ $date->year }},
        titreMois: "{{ $date->locale('fr')->translatedFormat('F Y') }}",

        typeOptions: [
            { value: 'ferie',     label: 'Férié',      bg: '#DBEAFE', color: '#1D4ED8' },
            /*{ value: 'repos',     label: 'Repos',       bg: '#F3F4F6', color: '#374151' },
            { value: 'evenement', label: 'Événement',   bg: '#DCFCE7', color: '#166534' },*/
        ],

        form: {
            id: null,
            date: '',
            type: 'ferie',
            titre: '',
            description: '',
            est_paye: false,
        },

        openDay(date) {

            this.selectedDate = date;


            this.selectedDateLabel = new Date(date)
                .toLocaleDateString('fr-FR', {
                    weekday:'long',
                    day:'numeric',
                    month:'long',
                    year:'numeric'
                });


            const jour = this.jours.find(
                j => j.date === date
            );


            this.selectedEvents = jour
                ? jour.evenements
                : [];


            this.dayModal = true;

        },

        openCreate(date) {
            this.editMode = false;
            this.form = { id: null, date: date, type: 'ferie', titre: '', description: '', est_paye: false };
            this.createModal = true;
        },

        openEdit(event) {
            this.editMode = true;
            this.form = {
                id: event.id,
                date: event.date,
                type: event.type,
                titre: event.titre,
                description: event.description ?? '',
                est_paye: event.est_paye,
            };
            this.dayModal = false;
            this.createModal = true;
        },

        confirmDelete(event) {
            this.deleteEvent = event;
            this.dayModal = false;
            this.deleteModal = true;
        },

        getBadgeColor(type) {
            const map = { ferie: '#1D4ED8'/*, repos: '#374151', evenement: '#166534'*/ };
            return map[type] || '#64748B';
        },

        getTypeLabel(type) {
            const map = { ferie: 'Férié'/*, repos: 'Repos', evenement: 'Événement'*/ };
            return map[type] || 'Autre';
        },

        async saveEvent(){

            const url = this.editMode
                ? `/calendrier/${this.form.id}`
                : `/calendrier`;


            const data = new FormData();


            data.append('_token',
                document.querySelector('meta[name="csrf-token"]').content
            );


            if(this.editMode){
                data.append('_method','PUT');
            }


            data.append('date', this.form.date);
            data.append('type', this.form.type);
            data.append('titre', this.form.titre);
            data.append('description', this.form.description);


            if(this.form.est_paye){
                data.append('est_paye',1);
            }


            try {


                const response = await fetch(url,{
                    method:'POST',
                    headers:{
                        Accept:'application/json'
                    },
                    body:data
                });


                const json = await response.json();


                if(!response.ok || !json.success){
                    throw json;
                }


                this.createModal=false;


                await this.refreshCalendar();

                this.selectedEvents = this.getEventsForDay(this.selectedDate);


            }catch(e){

                console.error(e);

            }

        },
        async deleteEventAjax(){


            try{


                const response = await fetch(
                    `/calendrier/${this.deleteEvent.id}`,
                    {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':
                            document.querySelector(
                            'meta[name="csrf-token"]'
                            ).content,

                            Accept:'application/json'
                        }
                    }
                );


                const json = await response.json();


                if(!response.ok || !json.success){
                    throw json;
                }


                this.deleteModal=false;


                await this.refreshCalendar();


            }catch(e){

                console.error(e);

            }

        },

        async refreshCalendar(){

            const response = await fetch(
                `/calendrier/navigation?mois=${this.mois}&annee=${this.annee}`
            );


            const json = await response.json();


            this.jours = json.jours;


            this.events = [];

            this.jours.forEach(jour => {

                jour.evenements.forEach(event => {

                    this.events.push({
                        ...event,
                        date:event.date.substring(0,10)
                    });

                });

            });

        },

        getEventsForDay(date){

            return this.events.filter(
                e => e.date === date
            );

        },

        init(){

            this.events = @json(
                collect($jours)
                    ->pluck('evenements')
                    ->flatten()
                    ->values()
            );

        },

        getEventsForDay(date){

            return this.events.filter(event => {

                return event.date.substring(0,10) === date;

            });

        },

        getBadge(type){

            const badges = {

                ferie:{
                    bg:'#DBEAFE',
                    color:'#1D4ED8'
                },

                repos:{
                    bg:'#F3F4F6',
                    color:'#374151'
                },

                evenement:{
                    bg:'#DCFCE7',
                    color:'#166534'
                }

            };


            return badges[type] ?? {
                bg:'#F1F5F9',
                color:'#64748B'
            };

        },

        async loadCalendar(){

            const response = await fetch(
                `/calendrier/data?mois=${this.mois}&annee=${this.annee}`
            );


            const json = await response.json();


            this.events = json.evenements.map(event=>({
                ...event,
                date:event.date.substring(0,10)
            }));


            this.titreMois=json.titre;


        },

        changeMonth(direction){

            let date = new Date(
                this.annee,
                this.mois-1,
                1
            );


            date.setMonth(
                date.getMonth()+direction
            );


            this.mois=date.getMonth()+1;
            this.annee=date.getFullYear();


            this.loadCalendar();

        },

        goToday(){

            let today=new Date();

            this.mois=today.getMonth()+1;
            this.annee=today.getFullYear();

            this.loadCalendar();

        },

        async changeMonth(mois, annee){

            const response = await fetch(
                `/calendrier/navigation?mois=${mois}&annee=${annee}`
            );


            const data = await response.json();


            this.mois = mois;
            this.annee = annee;

            this.titreMois = data.mois;

            this.jours = data.jours;


            // synchronisation événements
            this.events = [];

            data.jours.forEach(jour => {

                jour.evenements.forEach(event => {

                    this.events.push({
                        ...event,
                        date:event.date.substring(0,10)
                    });

                });

            });

        },


    };
}
</script>
</x-app-layout>