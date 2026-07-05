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
                {{-- NOUVEAU : Imprimer le bulletin de salaire de cet employé, pour la période affichée --}}
                <a href="{{ route('impressions.fiche-employe', array_filter([
                        'employe' => $employe->id,
                        'periode' => $periode,
                        'mois'    => request('mois'),
                        'annee'   => request('annee'),
                    ])) }}"
                target="_blank"
                class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center gap-1.5 transition-all hover:-translate-y-px"
                style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                    </svg>
                    Imprimer le bulletin
                </a>
            
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
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
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
        {{-- <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #0C447C">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Heures travaillées</p>
            <p class="text-[24px] font-black leading-none" style="color:#0C447C">{{ $stats['heures_total'] }}h</p>
            <p class="text-[10px] text-slate-400 mt-1">{{ $titre }}</p>
        </div> --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4" style="border-left:3px solid #1D4ED8">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Fériés Payés</p>
            <p class="text-[24px] font-black leading-none" style="color:#1D4ED8">{{ $stats['jours_feries_payes'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">jours</p>
        </div>
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
                                {{ $pointage->date ? $pointage->date->format('d/m/Y') : "-" }}
                            </td>
                            <td class="px-4 py-3 text-[12px] text-slate-500 capitalize">
                                @php
                                    $date = $pointage->date ? \Carbon\Carbon::parse($pointage->date) : null;
                                @endphp

                                {{ $date ? $date->locale('fr')->isoFormat('dddd') : "-" }}
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
                            {{--
                                <td class="px-4 py-3 text-[12px] text-slate-600">
                                    {{ $pointage->duree_formattee }}
                                </td>
                            --}}
                            
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
                @if($lignes->count() > 0)
                    <tfoot>
                        <tr style="background:#f8fafc; border-top:2px solid #e2e8f0">
                            <td colspan="4" class="px-4 py-3 text-[11px] font-bold text-slate-700">TOTAL</td>
                            {{-- <td class="px-4 py-3 text-[12px] font-bold" style="color:#0C447C">{{ $stats['heures_total'] }}h</td> --}}
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