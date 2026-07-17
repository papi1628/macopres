<x-app-layout>
<x-slot name="title">Fiche de production — {{ $bonCommande->numero }}</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-5">

    {{-- EN-TÊTE --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#F3E8FF; color:#7E22CE">Fiche de production</span>
                <h2 class="text-[16px] sm:text-[17px] font-bold text-slate-800 mt-1">{{ $bonCommande->programme->ecole->nom }}</h2>
                <p class="text-[11px] text-slate-400 mt-1">{{ $bonCommande->numero }} · {{ $bonCommande->date->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
                <a href="{{ route('programmes.bons.fiche.imprimer', $bonCommande) }}" target="_blank"
                   class="w-full sm:w-auto h-9 px-4 rounded-xl text-[12px] font-semibold text-white flex items-center justify-center gap-1.5"
                   style="background:linear-gradient(135deg,#7E22CE,#A855F7)">
                    Imprimer la fiche
                </a>
                <a href="{{ route('programmes.fiches.index', $bonCommande->programme) }}"
                   class="w-full sm:w-auto h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    <p class="text-[11px] text-slate-400 leading-5 px-2 sm:px-1">
        Les groupes ci-dessous sont formés automatiquement à partir des articles du bon de commande
        (même désignation + couleur + matière + logo = un seul groupe). Vous pouvez préciser une description
        et ajouter une photo pour chaque groupe — c'est ce que verra l'atelier de production.
    </p>

    {{-- GROUPES --}}
    @forelse($groupes as $groupe)
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800">{{ $groupe['libelle'] }}</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        {{ $groupe['couleur'] ?? '–' }}
                        @if($groupe['matiere']) · {{ $groupe['matiere'] }} @endif
                        @if($groupe['logo']) · avec logo @endif
                    </p>
                </div>
                <span class="text-[12px] font-bold self-start sm:self-auto" style="color:#0C447C">{{ $groupe['total'] }} pièce(s)</span>
            </div>

            <div class="p-4 sm:p-5 grid grid-cols-1 xl:grid-cols-2 gap-5"> 
                {{-- Répartition tailles --}}
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Répartition par taille</p>
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[240px] text-[12px]">
                            <tbody class="divide-y divide-slate-50">
                                @foreach($groupe['tailles'] as $taille => $quantite)
                                    <tr>
                                        <td class="py-1.5 text-slate-600">{{ $taille ?: '–' }}</td>
                                        <td class="py-1.5 text-right font-semibold">{{ $quantite }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background:#f8fafc">
                                    <td class="py-1.5 font-bold text-slate-700">TOTAL</td>
                                    <td class="py-1.5 text-right font-bold" style="color:#0C447C">{{ $groupe['total'] }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Description + photo --}}
                <div>
                    <form method="POST" action="{{ route('programmes.bons.fiche.note', $bonCommande) }}" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <input type="hidden" name="groupe_cle" value="{{ $groupe['cle'] }}">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Description (specs pour l'atelier)</label>
                            <textarea name="description" rows="3" placeholder="Ex : Col bleu de nuit, braguette 1 filé, manche simple..."
                                      class="w-full border border-slate-200 rounded-xl px-3 py-2 text-[12px] focus:outline-none focus:border-blue-400">{{ $groupe['note']->description ?? '' }}</textarea>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            @if($groupe['note']?->photoUrl())
                                <img src="{{ $groupe['note']->photoUrl() }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                            @endif
                            <input type="file" name="photo" accept="image/*" class="text-[11px] flex-1">
                        </div>
                        <button type="submit" class="w-full sm:w-auto h-9 px-4 rounded-lg text-[11px] font-bold text-white"style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                            Enregistrer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-[13px] text-slate-400">Aucun article dans ce bon de commande pour le moment.</p>
        </div>
    @endforelse
</div>
<style>
@media (max-width:640px){

    textarea{
        font-size:16px;
    }

    input[type=file]{
        width:100%;
        font-size:12px;
    }

    img.object-cover{
        width:70px;
        height:70px;
    }

}
</style>
</x-app-layout>