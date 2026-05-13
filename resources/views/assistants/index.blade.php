<x-app-layout>
<x-slot name="title">Assistants</x-slot>

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

<div x-data="assistantsApp()" class="space-y-4">

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
            <input x-model="search" type="text" placeholder="Rechercher un assistant"
                   class="w-full pl-9 pr-4 h-9 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        </div>
        
        <button @click="openCreate()"
                class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nouvel assistant
        </button>
    </div>

    {{-- ── Tableau ── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Matricule</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Employé</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Téléphone</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">login</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($assistants as $assistant)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-3 font-mono text-[11px] font-bold" style="color:#0C447C">{{ $assistant->employe?->matricule }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                     style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                                    {{ strtoupper(substr( $assistant->employe?->prenom ,0,1).substr($assistant->employe?->nom,0,1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-[13px]">{{  $assistant->employe?->prenom  }} {{ $assistant->employe?->nom }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-400 text-[12px]">{{ $assistant->employe?->tel ?? '–' }}</td>
                        <td class="px-4 py-3 text-slate-400 text-[12px]">{{ $assistant->login ?? '–' }}</td>                        
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5">
                                {{-- QR --}}
                                <button @click="openQR({{ $assistant->employe->id }})"
                                class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-blue-50" style="color:#185FA5" title="Badge QR"
                                style="color:#185FA5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                                </button>
                                {{-- Modifier --}}
                                <button @click="openEdit({{ $assistant->toJson() }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-amber-50 text-amber-500" title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                </button>
                                {{-- Supprimer --}}
                                <button @click="confirmDelete({{ $assistant->id }}, '{{  $assistant->employe?->prenom  }} {{ $assistant->employe?->nom }}')"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors hover:bg-red-50 text-red-400" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400 text-sm">Aucun assistant trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        @if ($assistants->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">{{ $assistants->total() }} assistant(s)</span>
            {{ $assistants->links() }}
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
                <h2 class="text-[15px] font-bold text-slate-800" x-text="editMode ? 'Modifier l\'assistant' : 'Nouvel assistant'"></h2>
                <button @click="modal = false" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl transition-colors">&times;</button>
            </div>

            {{-- Formulaire --}}
            <form :action="editMode ? `/assistants/${form.id}` : '{{ route('assistants.store') }}'" method="POST" class="px-6 py-5 space-y-4">
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

                    {{-- Date embauche --}}
                    <div x-show="!editMode">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date d'embauche</label>
                        <input type="date" name="date_embauche" x-model="form.date_embauche"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                    {{-- Salaire --}}
                    <div x-show="!editMode">
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Salaire (FCFA)</label>
                        <input type="number" name="salaire" x-model="form.salaire"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    </div>
                
                    
                    {{-- Login --}}
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Login</label>
                        <input type="text" name="login" x-model="form.login"
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
                        <span x-text="editMode ? 'Enregistrer' : 'Créer l\'assistant'"></span>
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
            <p class="text-xs text-slate-400 mb-5">Cette action est irréversible. <strong x-text="deleteName"></strong> ne pourra plus utiliser le système.</p>
            <div class="flex gap-3">
                <button @click="deleteModal = false"
                        class="flex-1 h-9 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50">Annuler</button>
                <form :action="'/assistants/' + deleteId" method="POST" class="flex-1">
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
function assistantsApp() {
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
        form: {
            id: null,
            prenom: '',
            nom: '',
            tel: '',
            departement: 'administration',
            date_embauche: '',
            salaire: '',
            login: '',
        },
        

        openCreate() {
            this.editMode = false;
            this.form = { 
                id: null,
                prenom: '',
                nom: '',
                tel: '',
                departement: 'administration',
                date_embauche: '',
                salaire: '',
                login: '',
            };
            
            this.modal = true;
            
        },

        openEdit(assistant) {
            this.editMode = true;
            this.form = {
                id: assistant.id,
                prenom: assistant.employe.prenom,
                nom: assistant.employe.nom,
                tel: assistant.employe.tel ?? '',
                date_embauche: assistant.employe.date_embauche
                    ? assistant.employe.date_embauche.split('T')[0]
                    : '',
                date_embauche: assistant.employe.date_embauche ?? '',
                salaire: assistant.employe.salaire ?? '',
                login: assistant.login ?? '',
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
        }
    };
}
</script>
</x-app-layout>