<x-app-layout>
<x-slot name="title">Programmes clients</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#FCEBEB; color:#A32D2D; border-color:#F5C0C0">
        {{ session('error') }}
    </div>
@endif

<div class="space-y-5">

    {{-- Toolbar --}}
    <div class="flex flex-wrap gap-3 items-center">
        <form method="GET" action="{{ route('programmes.index') }}" class="flex flex-wrap gap-3 flex-1">
            <select name="ecole_id" onchange="this.form.submit()"
                    class="h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white text-slate-700">
                <option value="">Toutes les écoles</option>
                @foreach($ecoles as $ecole)
                    <option value="{{ $ecole->id }}" {{ request('ecole_id') == $ecole->id ? 'selected' : '' }}>{{ $ecole->nom }}</option>
                @endforeach
            </select>
            <select name="annee_scolaire" onchange="this.form.submit()"
                    class="h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white text-slate-700">
                <option value="">Toutes les années</option>
                @foreach($anneesScolaires as $annee)
                    <option value="{{ $annee }}" {{ request('annee_scolaire') === $annee ? 'selected' : '' }}>{{ $annee }}</option>
                @endforeach
            </select>
            <select name="statut" onchange="this.form.submit()"
                    class="h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white text-slate-700">
                <option value="">Tous les statuts</option>
                <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours</option>
                <option value="termine" {{ request('statut') === 'termine' ? 'selected' : '' }}>Terminé</option>
                <option value="annule" {{ request('statut') === 'annule' ? 'selected' : '' }}>Annulé</option>
            </select>
            @if(request()->anyFilled(['ecole_id','annee_scolaire','statut']))
                <a href="{{ route('programmes.index') }}" class="h-9 px-3 rounded-xl text-sm border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center">Reset</a>
            @endif
        </form>

        <a href="{{ route('programmes.create') }}"
           class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
           style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nouveau programme
        </a>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[900px]">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">École</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Année scolaire</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Montant total</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Payé</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Solde</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Statut</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Détail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programmes as $programme)
                        @php
                            $montantTotal = $programme->montantTotal();
                            $paye = $programme->montantPaye();
                            $solde = $montantTotal - $paye;
                            $statutStyle = [
                                'en_cours' => ['En cours', '#DBEAFE', '#1D4ED8'],
                                'termine'  => ['Terminé', '#EAF3DE', '#3B6D11'],
                                'annule'   => ['Annulé', '#FCEBEB', '#A32D2D'],
                            ][$programme->statut];
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3 font-semibold text-slate-800 text-[13px]">{{ $programme->ecole->nom }}</td>
                            <td class="px-4 py-3 text-slate-500 text-[12px]">{{ $programme->annee_scolaire }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700 text-[12px]">{{ number_format($montantTotal, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-3 text-right text-[12px]" style="color:#3B6D11">{{ number_format($paye, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-3 text-right text-[12px] font-semibold" style="color:{{ $solde > 0 ? '#A32D2D' : '#3B6D11' }}">{{ number_format($solde, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:{{ $statutStyle[1] }}; color:{{ $statutStyle[2] }}">{{ $statutStyle[0] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('programmes.show', $programme) }}" class="text-[11px] font-semibold" style="color:#185FA5">Voir →</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-slate-400 text-sm">Aucun programme enregistré</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($programmes->hasPages())
            <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">{{ $programmes->total() }} programme(s)</span>
                {{ $programmes->links() }}
            </div>
        @endif
    </div>
</div>
</x-app-layout>