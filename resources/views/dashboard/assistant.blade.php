<x-app-layout>
    <x-slot name="title">Tableau de bord</x-slot>

    <div class="space-y-5">

        {{-- ══════════════════════════════════════
             KPI CARDS
        ══════════════════════════════════════ --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Employés --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5"
                 style="border-left:3px solid #0C447C">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Employés</p>
                <p class="text-[28px] font-black text-slate-800 leading-none">{{ $totalEmployes }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5">au total</p>
            </div>

            {{-- Présents aujourd'hui --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5"
                 style="border-left:3px solid #3B6D11">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Présents</p>
                <p class="text-[28px] font-black leading-none" style="color:#3B6D11">{{ $presents }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5">
                    {{ $totalEmployes > 0 ? round(($presents / $totalEmployes) * 100) : 0 }}% du personnel
                </p>
            </div>

            {{-- Retards --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5"
                 style="border-left:3px solid #854F0B">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Retards</p>
                <p class="text-[28px] font-black leading-none" style="color:#854F0B">{{ $retards }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5">Arrivée après 8h45</p>
            </div>

            {{-- Absents --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5"
                 style="border-left:3px solid #A32D2D">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Absents</p>
                <p class="text-[28px] font-black leading-none" style="color:#A32D2D">{{ $absents }}</p>
                <p class="text-[10px] text-slate-400 mt-1.5">
                    Non justifiés : {{ $absentsNonJustifies }}
                </p>
            </div>

        </div>

        {{-- ══════════════════════════════════════
             GRILLE PRINCIPALE
        ══════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:p-5">

            {{-- ── Présence par département ── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Présence par département</h3>
                    <span class="text-[10px] text-slate-400">Aujourd'hui</span>
                </div>
                <div class="px-5 py-4 space-y-3.5">
                    @forelse ($presenceParDept as $dept)
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-slate-500 w-28 flex-shrink-0 truncate">{{ $dept['nom'] }}</span>
                            <div class="flex-1 h-[5px] rounded-full overflow-hidden" style="background:#E6F1FB">
                                <div class="h-full rounded-full transition-all duration-500"
                                     style="width:{{ $dept['total'] > 0 ? round(($dept['presents'] / $dept['total']) * 100) : 0 }}%; background:#0C447C">
                                </div>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400 w-8 text-right flex-shrink-0">
                                {{ $dept['presents'] }}/{{ $dept['total'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-[12px] text-slate-400 text-center py-4">Aucune donnée disponible</p>
                    @endforelse
                </div>
            </div>

            {{-- ── Premières arrivées ── --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Premières arrivées</h3>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                          style="background:#E6F1FB; color:#0C447C">
                        {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMM YYYY') }}
                    </span>
                </div>
                <table class="w-full text-[11px]">
                    <thead>
                        <tr style="background:#f8fafc">
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider w-6">#</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Employée</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Dép.</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Heure</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($premieresArrivees as $index => $pointage)
                            <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5 font-bold text-[10px]" style="color:#0C447C">{{ $index + 1 }}</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[8px] font-bold text-white flex-shrink-0"
                                             style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                                            {{ mb_strtoupper(substr($pointage->employe->prenom, 0, 1) . substr($pointage->employe->nom, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-700">
                                            {{ $pointage->employe->prenom }} {{ $pointage->employe->nom }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-slate-400 text-[10px]">{{ $pointage->employe->departement }}</td>
                                <td class="px-4 py-2.5 font-mono text-[10px] text-slate-600">{{ $pointage->heure_arrivee }}</td>
                                <td class="px-4 py-2.5">
                                    @if ($pointage->statut === 'present')
                                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                                              style="background:#EAF3DE; color:#3B6D11">Présent</span>
                                    @elseif ($pointage->statut === 'retard')
                                        <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                                              style="background:#FAEEDA; color:#854F0B">Retard</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-[12px] text-slate-400">
                                    Aucune arrivée enregistrée aujourd'hui
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($premieresArrivees->count() > 0)
                    <div class="px-4 py-3 border-t border-slate-50">
                        <a href="{{ route('pointages.index') }}"
                           class="text-[11px] font-semibold transition-colors hover:underline"
                           style="color:#185FA5">
                            Voir tous les pointages →
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {{-- ══════════════════════════════════════
             LIGNE DU BAS : Répartition + Absents
        ══════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:p-5">

            {{-- Taux de présence global --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5">
                <h3 class="text-[12px] font-semibold text-slate-800 mb-4">Taux de présence</h3>
                @php
                    $taux = $totalEmployes > 0 ? round(($presents / $totalEmployes) * 100) : 0;
                @endphp
                <div class="flex items-end gap-3 mb-3">
                    <span class="text-[42px] font-black leading-none" style="color:#0C447C">{{ $taux }}</span>
                    <span class="text-[20px] font-bold text-slate-400 mb-1">%</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden mb-3" style="background:#E6F1FB">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width:{{ $taux }}%; background:linear-gradient(90deg,#0C447C,#378ADD)">
                    </div>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3">
                    @foreach ([['Présents',$presents,'#3B6D11'],['Retards',$retards,'#854F0B'],['Absents',$absents,'#A32D2D']] as [$label,$val,$color])
                        <div class="flex items-center gap-1.5">
                            <div class="w-2 h-2 rounded-full flex-shrink-0" style="background:{{ $color }}"></div>
                            <span class="text-[10px] text-slate-500">{{ $val }} {{ $label }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('pointages.statistiques') }}"
                class="text-[11px] font-semibold transition-colors hover:underline mt-3 inline-block"
                style="color:#185FA5">
                    Voir les statistiques détaillées →
                </a>
            </div>

            {{-- Absents du jour --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Absents du jour</h3>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                          style="background:#FCEBEB; color:#A32D2D">
                        {{ $absents }} absent(s)
                    </span>
                </div>
                <table class="w-full text-[11px]">
                    <thead>
                        <tr style="background:#f8fafc">
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Employée</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Département</th>
                            <th class="text-left px-4 py-2 text-[9px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absentsAujourdhui as $employe)
                            <tr class="border-t border-slate-50 hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-[8px] font-bold flex-shrink-0"
                                             style="background:#FCEBEB; color:#A32D2D">
                                            {{ mb_strtoupper(substr($employe->prenom, 0, 1) . substr($employe->nom, 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-slate-700">
                                            {{ $employe->prenom }} {{ $employe->nom }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5 text-slate-400 text-[10px]">{{ $employe->departement }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full"
                                          style="background:#FCEBEB; color:#A32D2D">Absent</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-[12px] text-slate-400">
                                    🎉 Tous les employés sont présents aujourd'hui !
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

    </div>

</x-app-layout>