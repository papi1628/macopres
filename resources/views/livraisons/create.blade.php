<x-app-layout>
<x-slot name="title">Nouvelle livraison — {{ $programme->ecole->nom }}</x-slot>

<div class="space-y-5 max-w-3xl">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <h2 class="text-[17px] font-bold text-slate-800">{{ $programme->ecole->nom }}</h2>
        <p class="text-[11px] text-slate-400 mt-1">Indiquez ce qui est livré aujourd'hui. Les quantités sont pré-remplies avec le reste à livrer — modifiez-les en cas de livraison partielle.</p>
    </div>

    @if($lignes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-[13px] text-slate-500">Tout ce qui a été commandé a déjà été livré.</p>
            <a href="{{ route('programmes.livraisons.index', $programme) }}" class="text-[12px] font-semibold mt-2 inline-block" style="color:#185FA5">← Retour aux livraisons</a>
        </div>
    @else
        <form method="POST" action="{{ route('programmes.livraisons.store', $programme) }}">
            @csrf

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Informations</h3>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Livreur</label>
                        <input type="text" name="livreur" placeholder="Nom du livreur"
                               class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Articles à livrer</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-[12px]">
                        <thead>
                            <tr class="border-b border-slate-100" style="background:#f8fafc">
                                <th class="text-left px-4 py-2.5 text-[9px] font-semibold text-slate-400 uppercase">Article</th>
                                <th class="text-left px-4 py-2.5 text-[9px] font-semibold text-slate-400 uppercase">Taille</th>
                                <th class="text-center px-4 py-2.5 text-[9px] font-semibold text-slate-400 uppercase">Reste à livrer</th>
                                <th class="text-center px-4 py-2.5 text-[9px] font-semibold text-slate-400 uppercase">Qté livrée aujourd'hui</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($lignes as $ligne)
                                <tr>
                                    <td class="px-4 py-2 font-semibold text-slate-700">{{ $ligne->libelle() }}</td>
                                    <td class="px-4 py-2 text-slate-500">{{ $ligne->taille ?? '–' }}</td>
                                    <td class="px-4 py-2 text-center text-slate-500">{{ $ligne->reste }}</td>
                                    <td class="px-4 py-2 text-center">
                                        <input type="number" name="quantites[{{ $ligne->id }}]" min="0" max="{{ $ligne->reste }}" value="0"
                                               class="w-20 h-8 border border-slate-200 rounded-lg px-2 text-center text-[12px]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('programmes.livraisons.index', $programme) }}"
                   class="h-10 px-5 rounded-xl text-sm font-semibold border border-slate-200 text-slate-500 hover:bg-slate-50 flex items-center">
                    Annuler
                </a>
                <button type="submit"
                        class="h-10 px-6 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#166534,#3B6D11)">
                    Valider la livraison
                </button>
            </div>
        </form>
    @endif
</div>
</x-app-layout>