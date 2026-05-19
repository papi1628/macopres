<x-app-layout>
<x-slot name="title">Fiche de présence</x-slot>

<div class="space-y-5">

    {{-- ══════════════════════════════════════
         EN-TÊTE EMPLOYÉ
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[14px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    {{ strtoupper(substr($employe->prenom,0,1).substr($employe->nom,0,1)) }}
                </div>
                <div>
                    <h2 class="text-[16px] font-bold text-slate-800">{{ $employe->prenom }} {{ $employe->nom }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-[10px] font-mono font-bold" style="color:#0C447C">{{ $employe->matricule }}</span>
                        @if($employe->departement)
                            <span class="text-[10px] text-slate-400">· {{ $employe->departement }}</span>
                        @endif
                        @if($employe->poste)
                            <span class="text-[10px] text-slate-400">· {{ $employe->poste }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('pointages.historique', ['employe_id' => $employe->id]) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors flex items-center">
                    Voir l'historique
                </a>
                <a href="{{ route('pointages.index') }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors flex items-center">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         ONGLETS PÉRIODE
    ══════════════════════════════════════ --}}
    <div class="flex gap-1 bg-white rounded-2xl border border-slate-100 shadow-sm p-1 w-fit">
        @foreach(['semaine' => 'Cette semaine', 'mois' => 'Ce mois', 'annee' => 'Cette année'] as $val => $label)
            <a href="{{ route('pointages.fiche-employe', ['employe' => $employe->id, 'periode' => $val]) }}"
               class="px-4 py-2 rounded-xl text-[12px] font-semibold transition-colors
                      {{ $periode === $val ? 'text-white' : 'text-slate-500 hover:bg-slate-50' }}"
               style="{{ $periode === $val ? 'background:linear-gradient(135deg,#185FA5,#378ADD)' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════
         STATS KPI
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #3B6D11">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Présences</p>
            <p class="text-[24px] font-black leading-none" style="color:#3B6D11">{{ $stats['jours_presents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">jours</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #A32D2D">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Absences</p>
            <p class="text-[24px] font-black leading-none" style="color:#A32D2D">{{ $stats['jours_absents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">jours</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #854F0B">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Retards</p>
            <p class="text-[24px] font-black leading-none" style="color:#854F0B">{{ $stats['jours_retard'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">jours</p>
        </div>
        <!--<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #0C447C">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Heures travaillées</p>
            <p class="text-[24px] font-black leading-none" style="color:#0C447C">{{ $stats['heures_total'] }}h</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ $titre }}</p>
        </div>-->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 col-span-2 lg:col-span-1" style="border-left:3px solid #185FA5">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Salaire</p>
            <p class="text-[20px] font-black leading-none" style="color:#185FA5">
                {{ number_format($stats['salaire_periode'], 0, ',', ' ') }}
            </p>
            <p class="text-[10px] text-slate-400 mt-1">FCFA · {{ $titre }}</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TABLEAU DÉTAILLÉ
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Détail — {{ $titre }}</h3>
            @if($stats['salaire_mensuel'])
                <span class="text-[10px] text-slate-400">
                    Salaire journalier : <strong class="text-slate-700">{{ number_format($stats['salaire_mensuel'], 0, ',', ' ') }} FCFA</strong>
                </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Jour</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Arrivée</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Départ</th>
                        <!--<th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Durée</th>-->
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Salaire/j</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($lignes as $pointage)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-mono text-[11px] font-bold" style="color:#0C447C">
                                {{ $pointage->date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3 text-[12px] text-slate-500 capitalize">
                                {{ $pointage->date->locale('fr')->isoFormat('dddd') }}
                            </td>
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
                            <!--<td class="px-4 py-3 text-[12px] text-slate-600">
                                {{ $pointage->duree_formattee }}
                            </td>-->
                            <td class="px-4 py-3 text-[12px] font-semibold text-slate-700">
                                {{ $pointage->salaire_jour ? number_format($pointage->salaire_jour, 0, ',', ' ') . ' F' : '–' }}
                            </td>
                            <td class="px-4 py-3">
                                @php $badge = $pointage->badge_statut; @endphp
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                                      style="background:{{ $badge['bg'] }}; color:{{ $badge['color'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-400 text-sm">
                                Aucun pointage pour cette période
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Ligne total --}}
                @if($pointages->count() > 0)
                    <tfoot>
                        <tr style="background:#f8fafc; border-top:2px solid #e2e8f0">
                            <td colspan="4" class="px-4 py-3 text-[11px] font-bold text-slate-700">TOTAL</td>
                            <!--<td class="px-4 py-3 text-[12px] font-bold" style="color:#0C447C">{{ $stats['heures_total'] }}h</td>-->
                            <td class="px-4 py-3 text-[12px] font-bold" style="color:#185FA5">
                                {{ number_format($stats['salaire_periode'], 0, ',', ' ') }} F
                            </td>
                            <td class="px-4 py-3 text-[11px] text-slate-400">
                                {{ $stats['jours_presents'] }} présence(s)
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
</x-app-layout>