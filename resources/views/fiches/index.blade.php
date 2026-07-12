<x-app-layout>
<x-slot name="title">Fiches de production — {{ $programme->ecole->nom }}</x-slot>

<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#F3E8FF; color:#7E22CE">Fiches de production</span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $programme->annee_scolaire }}</p>
            </div>
            <a href="{{ route('programmes.show', $programme) }}"
               class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                ← Retour au programme
            </a>
        </div>
    </div>

    {{-- LISTE (une fiche par BC) --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Commande</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Articles</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Document</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->bonsCommande as $i => $bon)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-[12px]" style="color:#0C447C">
                                {{ $bon->numero }}
                                <span class="text-[10px] font-normal text-slate-400 ml-1">
                                    ({{ $i === 0 ? 'fiche de base' : 'rajout' }})
                                </span>
                            </td>
                            <td class="px-4 py-3 text-[12px] text-slate-600">{{ $bon->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-center font-semibold text-[12px]">{{ $bon->lignes->sum('quantite') }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($bon->lignes->count() > 0)
                                    <a href="{{ route('programmes.bons.fiche.show', $bon) }}" class="text-[11px] font-semibold" style="color:#7E22CE">Gérer / Imprimer</a>
                                @else
                                    <span class="text-[11px] text-slate-300 italic">Aucun article</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune commande pour ce programme</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>