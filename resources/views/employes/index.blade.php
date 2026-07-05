<x-app-layout>
<x-slot name="title">Employés</x-slot>

{{-- ── Notifications flash ── --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-emerald-700 border border-emerald-200"
         style="background:#EAF3DE">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        {{ session('success') }}
    </div>
@endif

<div x-data="employesApp()" class="space-y-4">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input x-model="search" type="text" placeholder="Rechercher un employé…"
                   class="w-full pl-9 pr-4 h-9 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>

        {{-- Toggle mode sélection badges --}}
        <button @click="toggleSelectionMode()"
                class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold transition-all"
                :class="selectionMode ? 'text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'"
                :style="selectionMode ? 'background:linear-gradient(135deg,#0C447C,#185FA5)' : ''">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
            </svg>
            <span x-text="selectionMode ? 'Annuler la sélection' : 'Imprimer des badges'"></span>
        </button>

        <button @click="openCreate()"
                class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nouvel employé
        </button>
    </div>

    {{-- ── Barre flottante de sélection (visible en mode sélection) ── --}}
    <div x-show="selectionMode"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-2xl border shadow-sm p-4 flex flex-wrap items-center gap-4"
         style="border-color:#B5D4F4; background:#F5F9FE"
         style="display:none">

        <p class="text-[12px] font-semibold text-slate-700">
            <span class="font-bold" style="color:#0C447C" x-text="selectionnesBadges.length"></span>
            employé(s) sélectionné(s)
        </p>

        <div class="flex items-center gap-2">
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Format</span>
            <div class="flex gap-1 bg-white rounded-xl border border-slate-200 p-1">
                <button type="button" @click="formatBadge = 'unique'"
                        class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors"
                        :class="formatBadge === 'unique' ? 'text-white' : 'text-slate-500'"
                        :style="formatBadge === 'unique' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                    1 par page
                </button>
                <button type="button" @click="formatBadge = 'planche'"
                        class="px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-colors"
                        :class="formatBadge === 'planche' ? 'text-white' : 'text-slate-500'"
                        :style="formatBadge === 'planche' ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : ''">
                    Planche
                </button>
            </div>
        </div>

        <div class="flex-1"></div>

        <button type="button" @click="toutSelectionnerBadges()"
                class="text-[11px] font-semibold px-3 py-1.5 rounded-lg transition-colors"
                style="color:#185FA5; background:#E6F1FB">
            Tout sélectionner (page)
        </button>

        <button type="button" @click="imprimerBadges()"
                :disabled="selectionnesBadges.length === 0"
                :class="selectionnesBadges.length === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-px'"
                class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-bold text-white transition-all"
                style="background:linear-gradient(135deg,#0C447C,#185FA5)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
            </svg>
            Imprimer les badges
        </button>
    </div>

    {{-- ── Tableau ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-center px-3 py-3 w-8" x-show="selectionMode"></th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Matricule</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Employé</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Département</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Téléphone</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Code</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($employes as $employe)
                    <tr class="hover:bg-slate-50/60 transition-colors"
                        :class="selectionnesBadges.includes({{ $employe->id }}) ? 'bg-blue-50/50' : ''">
                        <td class="px-3 py-3 text-center" x-show="selectionMode">
                            <input type="checkbox"
                                   :value="{{ $employe->id }}"
                                   x-model="selectionnesBadges"
                                   class="w-4 h-4 rounded cursor-pointer"
                                   style="accent-color:#185FA5">
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] font-bold" style="color:#0C447C">{{ $employe->matricule }}</td>
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
                        <td class="px-4 py-3 text-slate-400 text-[12px]">{{ $employe->tel ?? '–' }}</td>
                        <td class="px-4 py-3 text-slate-400 text-[12px]">{{ $employe->salaire ?? '–' }}</td>

                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                {{-- QR --}}
                                <button @click="openQR({{ $employe->id }})"
                                class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-blue-50" style="color:#185FA5" title="Badge QR"
                                style="color:#185FA5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                                </button>

                                @php
                                    $canManage =
                                          Auth::user()->role !== 'assistant'
                                            || $employe->departement !== 'administration';
                                @endphp
                                @if($canManage)
                                    {{-- Modifier --}}
                                    <button @click="openEdit({{ $employe->toJson() }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-amber-50 text-amber-500" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    </button>
                                    {{-- Supprimer --}}
                                    <button @click="confirmDelete({{ $employe->id }}, '{{ $employe->prenom }} {{ $employe->nom }}')"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-red-50 text-red-400" title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400 text-sm">Aucun employé trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        @if ($employes->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">{{ $employes->total() }} employé(s)</span>
            {{ $employes->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════
         MODAL QR_CODE
    ══════════════════════════════════════ --}}

    <div x-show="qrModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        style="display:none">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center"
            @click.outside="qrModal = false">

            <h2 class="text-lg font-bold text-slate-800 mb-2" x-text="qrEmploye"></h2>

            <p class="text-xs text-slate-400 mb-4" x-text="qrMatricule"></p>

            <img :src="'data:image/svg+xml;base64,' + qr" class="mx-auto w-48 h-48"/>

            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" @click="qrModal = false"
                        class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                    Fermer
                </button>

                <button @click="downloadQR()"
                        class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Télécharger
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL CRÉER / MODIFIER
    ══════════════════════════════════════ --}}
    <div x-show="modal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="modal = false">

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="modal = false">

            {{-- Header modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="text-[15px] font-bold text-slate-800" x-text="editMode ? 'Modifier l\'employé' : 'Nouvel employé'"></h2>
                <button @click="modal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl transition-colors">&times;</button>
            </div>

            {{-- Formulaire --}}
            <form :action="editMode ? `/employes/${form.id}` : '{{ route('employes.store') }}'" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="grid grid-cols-2 gap-4">
                    {{-- Prénom --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Prénom *</label>
                        <input type="text" name="prenom" x-model="form.prenom" required
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                    {{-- Nom --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nom *</label>
                        <input type="text" name="nom" x-model="form.nom" required
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Téléphone</label>
                        <input type="text" name="tel" x-model="form.tel"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                               placeholder="77 000 00 00">
                    </div>
                    {{-- Département --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Département</label>
                        <select name="departement" x-model="form.departement" required
                                class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
                            <option value="">-- Choisir --</option>
                            @php
                                $departementsListe = [
                                    'salle de coupe',
                                    'salle de montage',
                                    'finition'
                                ];

                                if (Auth::user()->role === 'directeur') {
                                    array_unshift($departementsListe, 'administration');
                                }
                            @endphp
                            @foreach ($departementsListe as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Date embauche --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date d'embauche</label>
                        <input type="date" name="date_embauche" x-model="form.date_embauche"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                    {{-- Salaire --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Salaire (FCFA)</label>
                        <input type="number" name="salaire" x-model="form.salaire"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" @click="modal = false"
                            class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                            class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        <span x-text="editMode ? 'Enregistrer' : 'Créer l\'employé'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         MODAL CONFIRMATION SUPPRESSION
    ══════════════════════════════════════ --}}
    <div x-show="deleteModal"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:#FCEBEB">
                    <svg class="w-5 h-5" style="color:#A32D2D" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Confirmer la suppression</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Supprimer <strong x-text="deleteName"></strong> ?</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 mb-5">Cette action est irréversible. Les pointages associés seront conservés.</p>
            <div class="flex gap-3">
                <button @click="deleteModal = false"
                        class="flex-1 h-9 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50">Annuler</button>
                <form :action="'/employes/' + deleteId" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full h-9 rounded-xl text-sm font-bold text-white transition-colors hover:opacity-90"
                            style="background:#A32D2D">Supprimer</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function employesApp() {
    return {
        modal: false,
        deleteModal: false,
        editMode: false,
        qrModal: false,
        qr: '',
        qrEmploye: '',
        qrMatricule: '',
        deleteId: null,
        deleteName: '',
        search: '',
        filterDept: '',
        filterStatut: '',

        // Sélection pour impression des badges
        selectionMode: false,
        selectionnesBadges: [],
        formatBadge: 'unique',

        form: {
            id: null,
            prenom: '',
            nom: '',
            tel: '',
            departement: '',
            date_embauche: '',
            salaire: '',
        },

        openCreate() {
            this.editMode = false;
            this.form = {
                id: null,
                prenom: '',
                nom: '',
                tel: '',
                departement: '',
                date_embauche: '',
                salaire: '',
            };

            this.modal = true;

        },

        openEdit(employe) {
            this.editMode = true;
            this.form = {
                id: employe.id,
                prenom: employe.prenom,
                nom: employe.nom,
                tel: employe.tel ?? '',
                departement: employe.departement ?? '',
                date_embauche: employe.date_embauche
                    ? employe.date_embauche.split('T')[0]
                    : '',
                salaire: employe.salaire ?? '',
            };
            this.modal = true;
        },

        confirmDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteModal = true;
        },

        openQR(id) {
            fetch(`/employes/${id}/qr`)
                .then(res => res.json())
                .then(data => {
                    this.qr = data.qr;
                    this.qrEmploye = data.employe;
                    this.qrMatricule = data.matricule;
                    this.qrModal = true;
                })
                .catch(err => {
                    console.error('QR error:', err);
                });
        },

        downloadQR() {
            const link = document.createElement('a');
            link.href = 'data:image/svg+xml;base64,' + this.qr;
            link.download = this.qrMatricule + '.svg';
            link.click();
        },

        // ── Sélection / impression des badges ──
        toggleSelectionMode() {
            this.selectionMode = !this.selectionMode;
            this.selectionnesBadges = [];
        },

        toutSelectionnerBadges() {
            const ids = Array.from(document.querySelectorAll('input[type=checkbox][x-model]'))
                .map(el => el._x_model ? el._x_model.get() : null); // fallback non utilisé
            // Solution simple et fiable : re-sélectionne toutes les checkboxes visibles à l'écran
            const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
            this.selectionnesBadges = Array.from(checkboxes).map(cb => parseInt(cb.value));
        },

        imprimerBadges() {
            if (this.selectionnesBadges.length === 0) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("impressions.badges") }}';
            form.target = '_blank';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrf);

            this.selectionnesBadges.forEach(id => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'employe_ids[]';
                inp.value = id;
                form.appendChild(inp);
            });

            const fmt = document.createElement('input');
            fmt.type = 'hidden';
            fmt.name = 'format';
            fmt.value = this.formatBadge;
            form.appendChild(fmt);

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    };
}
</script>
</x-app-layout>