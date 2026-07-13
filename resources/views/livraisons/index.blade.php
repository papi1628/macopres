<x-app-layout>
<x-slot name="title">Livraisons — {{ $programme->ecole->nom }}</x-slot>

@if (session('success'))
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
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

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#DBFCE7; color:#166534">Livraisons</span>
                <h2 class="text-[17px] font-bold text-slate-800 mt-1">{{ $programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $programme->annee_scolaire }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('programmes.livraisons.suivi', $programme) }}" target="_blank"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                    Tableau de suivi
                </a>
                <a href="{{ route('programmes.livraisons.create', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center gap-1.5"
                   style="background:linear-gradient(135deg,#166534,#3B6D11)">
                    + Nouvelle livraison
                </a>
                <a href="{{ route('programmes.show', $programme) }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    {{-- LISTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">N° Bordereau</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Livreur</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Quantité livrée</th>
                        <th class="text-center px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase">Document</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->livraisons->sortByDesc('date') as $livraison)
                        <tr>
                            <td class="px-4 py-3 font-mono font-bold text-[12px]" style="color:#166534">{{ $livraison->numero }}</td>
                            <td class="px-4 py-3 text-[12px] text-slate-600">{{ $livraison->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-[12px] text-slate-700">{{ $livraison->livreur ?? '–' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-[12px]">{{ $livraison->quantite }} pièce(s)</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('programmes.livraisons.imprimer', $livraison) }}" target="_blank" class="text-[11px] font-semibold" style="color:#166534">Voir / Imprimer</a>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('programmes.livraisons.destroy', $livraison) }}" onsubmit="return confirm('Supprimer ce bordereau ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-[11px]">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune livraison enregistrée</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>