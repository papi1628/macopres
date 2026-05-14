<x-app-layout>
<x-slot name="title">Feuille de présence</x-slot>

{{-- ── Notifications ── --}}
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

<div x-data="pointageApp()" class="space-y-5">

    {{-- ══════════════════════════════════════
         TOOLBAR
    ══════════════════════════════════════ --}}
    <div class="flex flex-wrap gap-3 items-center">

        {{-- Sélecteur de date --}}
        <form method="GET" action="{{ route('pointages.index') }}" class="flex items-center gap-2">
            <input type="date" name="date"
                   value="{{ $date->format('Y-m-d') }}"
                   max="{{ today()->format('Y-m-d') }}"
                   onchange="this.form.submit()"
                   class="h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
        </form>

        {{-- Recherche --}}
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
            </svg>
            <input x-model="search" type="text" placeholder="Rechercher un employé…"
                   class="w-full pl-9 pr-4 h-9 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100 bg-white">
        </div>

        {{-- Bouton pointer --}}
        <button @click="openPointer()"
                class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Pointer un employé
        </button>

        {{-- Scanner QR --}}
        <a href="{{ route('pointages.scan') }}"
           class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
           style="background:linear-gradient(135deg,#0C447C,#185FA5)">

            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
            </svg>
            Scanner QR
        </a>

        <a href="{{ route('pointages.statistiques') }}"
            class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
            </svg>
            Statistiques
        </a>
    </div>

    {{-- ══════════════════════════════════════
         STATS DU JOUR
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #0C447C">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total</p>
            <p class="text-[26px] font-black text-slate-800 leading-none">{{ $stats['total'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">employés</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #3B6D11">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Présents</p>
            <p class="text-[26px] font-black leading-none" style="color:#3B6D11">{{ $stats['presents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ $stats['total'] > 0 ? round(($stats['presents'] / $stats['total']) * 100) : 0 }}% du personnel</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #854F0B">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Retards</p>
            <p class="text-[26px] font-black leading-none" style="color:#854F0B">{{ $stats['retards'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">après 8h45</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #A32D2D">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Absents</p>
            <p class="text-[26px] font-black leading-none" style="color:#A32D2D">{{ $stats['absents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">non pointés</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TABLEAU DES EMPLOYÉS
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">
                Présence du {{ $date->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
            </h3>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                  style="background:#E6F1FB; color:#0C447C">
                {{ $pointagesDuJour->count() }} / {{ $employes->count() }} pointés
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Matricule</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Employé</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Département</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Arrivée</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Départ</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Durée</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($employes as $employe)
                        @php $pointage = $pointagesDuJour->get($employe->id); @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors"
                            x-show="!search || '{{ strtolower($employe->prenom . ' ' . $employe->nom . ' ' . $employe->matricule . ' ' . $employe->departement) }}'.includes(search.toLowerCase())">

                            <td class="px-4 py-3 font-mono text-[11px] font-bold" style="color:#0C447C">
                                {{ $employe->matricule }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                         style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                                        {{ strtoupper(substr($employe->prenom,0,1).substr($employe->nom,0,1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-800 text-[13px]">{{ $employe->prenom }} {{ $employe->nom }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-500 text-[12px]">{{ $employe->departement ?? '–' }}</td>

                            {{-- Arrivée --}}
                            <td class="px-4 py-3 font-mono text-[12px] text-slate-700">
                                @if($pointage)
                                    {{ $pointage->heure_arrivee ? substr($pointage->heure_arrivee, 0, 5) : '–' }}
                                    @if($pointage->retard)
                                        <span class="text-[9px] font-semibold ml-1 px-1.5 py-0.5 rounded-full"
                                              style="background:#FAEEDA; color:#854F0B">
                                            +{{ $pointage->minutes_retard }}min
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300">–</span>
                                @endif
                            </td>

                            {{-- Départ --}}
                            <td class="px-4 py-3 font-mono text-[12px] text-slate-700">
                                @if($pointage && $pointage->heure_depart)
                                    {{ substr($pointage->heure_depart, 0, 5) }}
                                @elseif($pointage)
                                    <button @click="openDepart({{ $pointage->id }}, '{{ $employe->prenom }} {{ $employe->nom }}')"
                                            class="text-[10px] font-semibold px-2 py-1 rounded-lg transition-colors hover:opacity-80"
                                            style="background:#E6F1FB; color:#0C447C">
                                        + Départ
                                    </button>
                                @else
                                    <span class="text-slate-300">–</span>
                                @endif
                            </td>

                            {{-- Durée --}}
                            <td class="px-4 py-3 text-[12px] text-slate-600">
                                {{ $pointage ? $pointage->duree_formattee : '–' }}
                            </td>

                            {{-- Statut --}}
                            <td class="px-4 py-3">
                                @if($pointage)
                                    @php $badge = $pointage->badge_statut; @endphp
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                          style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                @else
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                          style="background:#FCEBEB; color:#A32D2D">Absent</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    @if(!$pointage)
                                        {{-- Pointer maintenant --}}
                                        <button @click="pointerRapide({{ $employe->id }}, '{{ $employe->prenom }} {{ $employe->nom }}')"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-green-50"
                                                style="color:#3B6D11" title="Pointer maintenant">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Fiche employé --}}
                                    <a href="{{ route('pointages.fiche-employe', $employe) }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-blue-50"
                                       style="color:#185FA5" title="Fiche de présence">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                                        </svg>
                                    </a>

                                    {{-- Supprimer pointage --}}
                                    @if($pointage)
                                        <form method="POST" action="{{ route('pointages.destroy', $pointage) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    onclick="return confirm('Supprimer ce pointage ?')"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-red-50 text-red-400"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL — POINTER UN EMPLOYÉ
    ══════════════════════════════════════ --}}
    <div x-show="pointerModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="pointerModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             @click.outside="pointerModal = false">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[15px] font-bold text-slate-800">Pointer un employé</h2>
                <button @click="pointerModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl">&times;</button>
            </div>

            <form method="POST" action="{{ route('pointages.pointer') }}" class="px-6 py-5 space-y-4">
                @csrf

                {{-- Recherche employé --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                        Rechercher l'employé *
                    </label>
                    <input type="text" x-model="employeSearch" placeholder="Nom, prénom ou matricule…"
                           @input="filterEmployes()"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">

                    {{-- Résultats --}}
                    <div x-show="employeSearch.length > 1 && resultats.length > 0"
                         class="mt-1 border border-slate-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto">
                        <template x-for="emp in resultats" :key="emp.id">
                            <button type="button"
                                    @click="selectEmploye(emp)"
                                    class="w-full flex items-center gap-3 px-3 py-2.5 hover:bg-blue-50 transition-colors text-left border-b border-slate-50 last:border-0">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                     style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                                     x-text="emp.initiales"></div>
                                <div>
                                    <p class="text-[13px] font-semibold text-slate-800" x-text="emp.prenom + ' ' + emp.nom"></p>
                                    <p class="text-[10px] text-slate-400" x-text="emp.matricule + ' · ' + (emp.departement ?? '')"></p>
                                </div>
                            </button>
                        </template>
                    </div>

                    {{-- Employé sélectionné --}}
                    <div x-show="selectedEmploye" class="mt-2 flex items-center gap-3 px-3 py-2.5 rounded-xl border"
                         style="background:#E6F1FB; border-color:#B5D4F4">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                             x-text="selectedEmploye?.initiales"></div>
                        <div class="flex-1">
                            <p class="text-[13px] font-semibold" style="color:#0C447C"
                               x-text="selectedEmploye?.prenom + ' ' + selectedEmploye?.nom"></p>
                            <p class="text-[10px]" style="color:#185FA5"
                               x-text="selectedEmploye?.matricule"></p>
                        </div>
                        <button type="button" @click="selectedEmploye = null; employeSearch = ''"
                                class="text-blue-400 hover:text-blue-600 text-lg">&times;</button>
                    </div>

                    <input type="hidden" name="employe_id" :value="selectedEmploye?.id">
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date *</label>
                    <input type="date" name="date"
                           :value="selectedDate"
                           max="{{ today()->format('Y-m-d') }}"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>

                {{-- Heure d'arrivée --}}
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Heure d'arrivée *</label>
                    <input type="time" name="heure_arrivee"
                           :value="currentTime"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <p class="text-[10px] text-slate-400 mt-1">Heure limite sans retard : 08h45</p>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="pointerModal = false"
                            class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        Enregistrer l'arrivée
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL — ENREGISTRER LE DÉPART
    ══════════════════════════════════════ --}}
    <div x-show="departModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="departModal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm"
             @click.outside="departModal = false">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[15px] font-bold text-slate-800">Enregistrer le départ</h2>
                <button @click="departModal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl">&times;</button>
            </div>

            <form :action="'/pointages/' + departPointageId + '/depart'" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')

                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl" style="background:#E6F1FB">
                    <svg class="w-4 h-4" style="color:#0C447C" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    <span class="text-[13px] font-semibold" style="color:#0C447C" x-text="departNom"></span>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Heure de départ *</label>
                    <input type="time" name="heure_depart" :value="currentTime"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="departModal = false"
                            class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
// Données employés pour la recherche
const EMPLOYES = @json($employesJson);

function pointageApp() {
    return {
        search: '',
        pointerModal: false,
        departModal: false,
        employeSearch: '',
        resultats: [],
        selectedEmploye: null,
        selectedDate: '{{ $date->format('Y-m-d') }}',
        currentTime: new Date().toTimeString().slice(0,5),
        departPointageId: null,
        departNom: '',

        openPointer() {
            this.currentTime = new Date().toTimeString().slice(0,5);
            this.pointerModal = true;
        },

        pointerRapide(id, nom) {
            this.selectedEmploye = EMPLOYES.find(e => e.id === id);
            this.employeSearch = nom;
            this.currentTime = new Date().toTimeString().slice(0,5);
            this.pointerModal = true;
        },

        openDepart(pointageId, nom) {
            this.departPointageId = pointageId;
            this.departNom = nom;
            this.currentTime = new Date().toTimeString().slice(0,5);
            this.departModal = true;
        },

        filterEmployes() {
            if (this.employeSearch.length < 2) { this.resultats = []; return; }
            const q = this.employeSearch.toLowerCase();
            this.resultats = EMPLOYES.filter(e =>
                (e.prenom + ' ' + e.nom + ' ' + e.matricule + ' ' + (e.departement ?? '')).toLowerCase().includes(q)
            ).slice(0, 6);
        },

        selectEmploye(emp) {
            this.selectedEmploye = emp;
            this.employeSearch = emp.prenom + ' ' + emp.nom;
            this.resultats = [];
        },
    };
}
</script>
</x-app-layout>