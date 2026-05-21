{{-- ══════════════════════════════════════
     MODAL — FÉRIÉ PAYÉ
     S'ouvre quand la date est un jour férié
     décréé par le directeur
══════════════════════════════════════ --}}
<div x-show="fpModal"
     x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     style="display:none"
     @keydown.escape.window="fpModal = false">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg"
         @click.outside="fpModal = false">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:#DBEAFE">
                    <svg class="w-5 h-5" style="color:#1D4ED8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-[15px] font-bold text-slate-800">Jour Férié Payé</h2>
                    <p class="text-[11px] font-semibold mt-0.5" style="color:#1D4ED8" x-text="fpEvenementTitre"></p>
                </div>
            </div>
            <button @click="fpModal = false"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 text-xl">&times;</button>
        </div>

        <div class="px-6 py-5 space-y-4">

            {{-- Employé concerné --}}
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl"
                 style="background:#E6F1FB">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                     x-text="fpEmployeInitiales"></div>
                <div>
                    <p class="text-[13px] font-bold" style="color:#0C447C" x-text="fpEmployeNom"></p>
                    <p class="text-[10px]" style="color:#185FA5">Salaire journalier : <span class="font-bold" x-text="fpSalaireFormate"></span></p>
                </div>
            </div>

            {{-- 5 derniers pointages --}}
            <div>
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-2">5 derniers jours</p>
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    <template x-if="fpDerniersPointages.length === 0">
                        <p class="text-[12px] text-slate-400 text-center py-3">Aucun historique disponible</p>
                    </template>
                    <template x-for="p in fpDerniersPointages" :key="p.id ?? p.date">
                        <div class="flex items-center justify-between px-3 py-2 rounded-xl"
                             :style="p.statut === 'absent' ? 'background:#FFF5F5' : (p.statut === 'ferie_paye' ? 'background:#EFF6FF' : 'background:#F8FAFC')">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                     :style="p.statut === 'present' ? 'background:#3B6D11' : p.statut === 'retard' ? 'background:#854F0B' : p.statut === 'ferie_paye' ? 'background:#1D4ED8' : 'background:#A32D2D'">
                                </div>
                                <span class="text-[12px] text-slate-600 capitalize" x-text="formatDate(p.date)"></span>
                            </div>
                            <div class="flex items-center gap-2">
    
                                <template x-if="p.evenement_titre">
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold"
                                        style="background:#DBEAFE;color:#1D4ED8"
                                        x-text="p.evenement_titre">
                                    </span>
                                </template>

                                {{-- <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                    :style="getBadgeStyle(p.statut)"
                                    x-text="getStatutLabel(p.statut)">
                                </span> --}}

                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Informations --}}
            <div class="flex items-start gap-2 p-3 rounded-xl" style="background:#FFFBEB">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:#854F0B" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
                <p class="text-[11px]" style="color:#854F0B">
                    En cliquant <strong>Férié Payé</strong>, l'employé recevra son salaire journalier complet pour ce jour.
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" @click="fpModal = false"
                        class="flex-1 h-10 border border-slate-200 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 transition-colors">
                    Annuler
                </button>

                <form method="POST" action="{{ route('pointages.pointer-fp') }}" class="flex-1">
                    @csrf
                    <input type="hidden" name="employe_id" :value="fpEmployeId">
                    <input type="hidden" name="date" :value="fpDate">
                    <button type="submit"
                            :disabled="!fpEmployeId"
                            :class="!fpEmployeId
                                ? 'opacity-50 cursor-not-allowed'
                                : 'hover:-translate-y-px'"
                            class="w-full h-10 rounded-xl text-sm font-bold text-white transition-all"
                            style="background:linear-gradient(135deg,#1D4ED8,#3B82F6)">
                        Valider le Férié Payé
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>