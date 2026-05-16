<x-app-layout>
<x-slot name="title">Statistiques de présence</x-slot>

<div class="space-y-5">

    {{-- ══════════════════════════════════════
         SÉLECTEUR DE MOIS
    ══════════════════════════════════════ --}}
    <form method="GET" action="{{ route('pointages.statistiques') }}"
          class="flex items-center gap-3 flex-wrap">
        <div>
            <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Période</label>
            <input type="month" name="mois" value="{{ $mois }}"
                   onchange="this.form.submit()"
                   class="h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 bg-white">
        </div>
        <div class="flex gap-2 mt-4">
            @php
                $moisPrecedent = \Carbon\Carbon::parse($mois . '-01')->subMonth()->format('Y-m');
                $moisSuivant  = \Carbon\Carbon::parse($mois . '-01')->addMonth()->format('Y-m');
            @endphp
            <a href="{{ route('pointages.statistiques', ['mois' => $moisPrecedent]) }}"
               class="h-9 px-3 rounded-xl text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors flex items-center">
                ← Mois préc.
            </a>
            <a href="{{ route('pointages.statistiques', ['mois' => now()->format('Y-m')]) }}"
               class="h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px flex items-center"
               style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                Ce mois
            </a>
            @if($moisSuivant <= now()->format('Y-m'))
                <a href="{{ route('pointages.statistiques', ['mois' => $moisSuivant]) }}"
                   class="h-9 px-3 rounded-xl text-sm border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors flex items-center">
                    Mois suiv. →
                </a>
            @endif
        </div>
        <div class="mt-4 text-[11px] text-slate-400 font-medium">
            {{ \Carbon\Carbon::parse($mois . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}
        </div>
    </form>

    {{-- ══════════════════════════════════════
         KPI GLOBAUX DU MOIS
    ══════════════════════════════════════ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" style="border-left:3px solid #3B6D11">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Présences</p>
            <p class="text-[28px] font-black leading-none" style="color:#3B6D11">{{ $statsGlobales['total_presents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">pointages présents</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" style="border-left:3px solid #A32D2D">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Absences</p>
            <p class="text-[28px] font-black leading-none" style="color:#A32D2D">{{ $statsGlobales['total_absents'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">journées d'absence</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" style="border-left:3px solid #854F0B">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Retards</p>
            <p class="text-[28px] font-black leading-none" style="color:#854F0B">{{ $statsGlobales['total_retards'] }}</p>
            <p class="text-[10px] text-slate-400 mt-1">arrivées tardives</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5" style="border-left:3px solid #185FA5">
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Masse salariale</p>
            <p class="text-[22px] font-black leading-none" style="color:#185FA5">
                {{ number_format($statsGlobales['total_salaires'], 0, ',', ' ') }}
            </p>
            <p class="text-[10px] text-slate-400 mt-1">FCFA versés ce mois</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TAUX DE PRÉSENCE GLOBAL
    ══════════════════════════════════════ --}}
    @php
        $totalPossible = $statsGlobales['total_presents'] + $statsGlobales['total_absents'];
        $tauxGlobal    = $totalPossible > 0 ? round(($statsGlobales['total_presents'] / $totalPossible) * 100) : 0;
    @endphp
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[12px] font-semibold text-slate-800">Taux de présence global</h3>
            <span class="text-[24px] font-black" style="color:#0C447C">{{ $tauxGlobal }}%</span>
        </div>
        <div class="h-3 rounded-full overflow-hidden" style="background:#E6F1FB">
            <div class="h-full rounded-full transition-all duration-700"
                 style="width:{{ $tauxGlobal }}%; background:linear-gradient(90deg,#0C447C,#378ADD)">
            </div>
        </div>
        <div class="flex flex-wrap gap-x-6 gap-y-1 mt-3">
            @foreach([
                ['Présents', $statsGlobales['total_presents'],  '#3B6D11'],
                ['Retards',  $statsGlobales['total_retards'],   '#854F0B'],
                ['Absents',  $statsGlobales['total_absents'],   '#A32D2D'],
                ['Total heures', $statsGlobales['total_heures'] . 'h', '#185FA5'],
            ] as [$label, $val, $color])
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full" style="background:{{ $color }}"></div>
                    <span class="text-[10px] text-slate-500">{{ $val }} {{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════
         TABLEAU PAR EMPLOYÉ
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">
                Détail par employé —
                {{ \Carbon\Carbon::parse($mois . '-01')->locale('fr')->isoFormat('MMMM YYYY') }}
            </h3>
            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                  style="background:#E6F1FB; color:#0C447C">
                {{ $employes->count() }} employé(s)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Employé</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Département</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Présences</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Absences</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Retards</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Heures</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Taux</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Salaire dû</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Salaire base</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Fiche</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($employes as $row)
                        @php
                            $totalJours = $row['jours_presents'] + $row['jours_absents'];
                            $taux = $totalJours > 0 ? round(($row['jours_presents'] / $totalJours) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    
                                    <div>
                                        <p class="font-semibold text-slate-800 text-[12px]">
                                            {{ $row['employe']->prenom }} {{ $row['employe']->nom }}
                                        </p>
                                        <p class="text-[10px] font-mono" style="color:#0C447C">
                                            {{ $row['employe']->matricule }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-500 text-[12px]">
                                {{ $row['employe']->departement ?? '–' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[13px] font-bold" style="color:#3B6D11">
                                    {{ $row['jours_presents'] }}j
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[13px] font-bold {{ $row['jours_absents'] > 0 ? '' : 'text-slate-300' }}"
                                      style="{{ $row['jours_absents'] > 0 ? 'color:#A32D2D' : '' }}">
                                    {{ $row['jours_absents'] }}j
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[13px] font-bold {{ $row['retards'] > 0 ? '' : 'text-slate-300' }}"
                                      style="{{ $row['retards'] > 0 ? 'color:#854F0B' : '' }}">
                                    {{ $row['retards'] }}x
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-[12px] text-slate-600 font-mono">
                                {{ $row['heures_total'] }}h
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background:#E6F1FB">
                                        <div class="h-full rounded-full"
                                             style="width:{{ $taux }}%; background:{{ $taux >= 80 ? '#3B6D11' : ($taux >= 50 ? '#854F0B' : '#A32D2D') }}">
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold w-8 text-right"
                                          style="color:{{ $taux >= 80 ? '#3B6D11' : ($taux >= 50 ? '#854F0B' : '#A32D2D') }}">
                                        {{ $taux }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-[13px] font-bold text-slate-800">
                                    {{ number_format($row['salaire_du'], 0, ',', ' ') }} F
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-[12px] text-slate-400">
                                {{ $row['salaire_base'] ? number_format($row['salaire_base'], 0, ',', ' ') . ' F' : '–' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('pointages.fiche-employe', $row['employe']) }}"
                                   class="w-7 h-7 inline-flex items-center justify-center rounded-lg hover:bg-blue-50 transition-colors"
                                   style="color:#185FA5" title="Voir la fiche">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                {{-- Ligne totaux --}}
                <tfoot>
                    <tr style="background:#f0f4f8; border-top:2px solid #e2e8f0">
                        <td colspan="2" class="px-4 py-3 text-[11px] font-bold text-slate-700">TOTAL DU MOIS</td>
                        <td class="px-4 py-3 text-center text-[12px] font-bold" style="color:#3B6D11">
                            {{ $employes->sum('jours_presents') }}j
                        </td>
                        <td class="px-4 py-3 text-center text-[12px] font-bold" style="color:#A32D2D">
                            {{ $employes->sum('jours_absents') }}j
                        </td>
                        <td class="px-4 py-3 text-center text-[12px] font-bold" style="color:#854F0B">
                            {{ $employes->sum('retards') }}x
                        </td>
                        <td class="px-4 py-3 text-center text-[12px] font-bold text-slate-700">
                            {{ $employes->sum('heures_total') }}h
                        </td>
                        <td class="px-4 py-3"></td>
                        <td class="px-4 py-3 text-right text-[13px] font-black" style="color:#0C447C">
                            {{ number_format($employes->sum('salaire_du'), 0, ',', ' ') }} F
                        </td>
                        // Après — on n'affiche pas de total pour le salaire de base, ça n'a pas de sens
                        <td class="px-4 py-3 text-right text-[12px] text-slate-400">–</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
</x-app-layout>