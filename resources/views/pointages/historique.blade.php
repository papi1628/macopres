<x-app-layout>
<x-slot name="title">Historique des pointages</x-slot>

<div class="space-y-5">

    {{-- ══════════════════════════════════════
         FILTRES
    ══════════════════════════════════════ --}}
    @php
        $impressionDebut = request('date_debut') ?: \Carbon\Carbon::parse(request('mois', now()->format('Y-m')) . '-01')->startOfMonth()->format('Y-m-d');
        $impressionFin   = request('date_fin')   ?: \Carbon\Carbon::parse(request('mois', now()->format('Y-m')) . '-01')->endOfMonth()->format('Y-m-d');
    @endphp
    
    <form method="GET" action="{{ route('pointages.historique') }}"
        class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
        <div class="flex flex-wrap gap-3 items-end">
    
            <div class="flex-1 min-w-[140px]">
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Employé</label>
                <select name="employe_id"
                        class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
                    <option value="">Tous les emp...</option>
                    @foreach($employes as $emp)
                        <option value="{{ $emp->id }}" {{ request('employe_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->prenom }} {{ $emp->nom }}
                        </option>
                    @endforeach
                </select>
            </div>
    
            <div class="flex-1 min-w-[120px]">
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Statut</label>
                <select name="statut"
                        class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white text-slate-700">
                    <option value="">Tous</option>
                    <option value="present"  {{ request('statut') === 'present'  ? 'selected' : '' }}>Présent</option>
                    <option value="retard"   {{ request('statut') === 'retard'   ? 'selected' : '' }}>Retard</option>
                    <option value="absent"   {{ request('statut') === 'absent'   ? 'selected' : '' }}>Absent</option>
                </select>
            </div>
    
            <div class="flex-1 min-w-[120px]">
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mois</label>
                <input type="month" name="mois"
                    value="{{ request('mois', now()->format('Y-m')) }}"
                    class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
            </div>
    
            <div class="flex-1 min-w-[120px]">
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date début</label>
                <input type="date" name="date_debut"
                    value="{{ request('date_debut') }}"
                    class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
            </div>
    
            <div class="flex-1 min-w-[120px]">
                <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date fin</label>
                <input type="date" name="date_fin"
                    value="{{ request('date_fin') }}"
                    class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
            </div>
    
            <div class="flex gap-2">
                <button type="submit"
                        class="h-9 px-5 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Filtrer
                </button>
                <a href="{{ route('pointages.historique') }}"
                class="h-9 px-4 rounded-xl text-sm font-semibold border border-slate-200 text-slate-500 hover:bg-slate-50 transition-colors flex items-center">
                    Reset
                </a>
                {{-- NOUVEAU : Imprimer la période affichée --}}
                <a href="{{ route('impressions.feuille-presence', ['date_debut' => $impressionDebut, 'date_fin' => $impressionFin]) }}"
                target="_blank"
                class="h-9 px-4 rounded-xl text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                    </svg>
                    Imprimer
                </a>
            </div>
        </div>
    </form>

    {{-- ══════════════════════════════════════
         TABLEAU
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Historique</h3>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                  style="background:#E6F1FB; color:#0C447C">
                {{ $pointages->total() }} pointage(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[800px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Employé</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Département</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Arrivée</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Départ</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Salaire/j</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Type</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pointages as $pointage)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-mono text-[11px] font-bold" style="color:#0C447C">
                                {{ $pointage->date->locale('fr')->isoFormat('ddd D MMM') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    
                                    <div>
                                        <p class="font-semibold text-slate-800 text-[12px]">
                                            {{ $pointage->employe->prenom }} {{ $pointage->employe->nom }}
                                        </p>
                                        <p class="text-[10px] font-mono" style="color:#0C447C">{{ $pointage->employe->matricule }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-[12px]">{{ $pointage->employe->departement ?? '–' }}</td>
                            <td class="px-4 py-3 font-mono text-[12px] text-slate-700">
                                {{ $pointage->heure_arrivee ? substr($pointage->heure_arrivee, 0, 5) : '–' }}
                                @if($pointage->retard)
                                    <span class="text-[9px] ml-1 font-semibold px-1.5 py-0.5 rounded-full"
                                          style="background:#FAEEDA; color:#854F0B">
                                        +{{ $pointage->minutes_retard }}min
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-mono text-[12px] text-slate-700">
                                {{ $pointage->heure_depart ? substr($pointage->heure_depart, 0, 5) : '–' }}
                            </td>
                            
                            <td class="px-4 py-3 text-[12px] font-semibold text-slate-700">
                                {{ $pointage->salaire_jour ? number_format($pointage->salaire_jour, 0, ',', ' ') . ' F' : '–' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                      style="background:#f1f5f9; color:#475569">
                                    {{ $pointage->type === 'qr_code' ? 'QR Code' : 'Manuel' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php $badge = $pointage->badge_statut; @endphp
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                      style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('pointages.fiche-employe', $pointage->employe) }}"
                                       class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-blue-50 transition-colors"
                                       style="color:#185FA5" title="Fiche employé">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('pointages.destroy', $pointage) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Supprimer ce pointage ?')"
                                                class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-red-50 text-red-400 transition-colors"
                                                title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-slate-400 text-sm">
                                Aucun pointage trouvé pour ces critères
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pointages->hasPages())
            <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">{{ $pointages->total() }} résultat(s)</span>
                {{ $pointages->links() }}
            </div>
        @endif
    </div>

</div>
</x-app-layout>