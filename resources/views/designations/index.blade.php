<x-app-layout>
<x-slot name="title">Catalogue des désignations</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif

<div class="space-y-5 max-w-3xl">

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <p class="text-[12px] text-slate-500">
            Le catalogue des désignations permet de choisir rapidement un article (chemise, polo, pantalon...)
            lors de la création d'un bon de commande, avec un prix par défaut déjà renseigné.
        </p>
    </div>

    @if(Auth::user()->role === 'directeur')
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-4 sm:px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Ajouter une désignation</h3>
        </div>
        <form method="POST" action="{{ route('designations.store') }}" class="p-5 grid grid-cols-1 sm:grid-cols-4 gap-3">
            @csrf
            <input type="text" name="nom" placeholder="Ex : Chemise, Polo, Pantalon" required
                   class="h-9 border border-slate-200 rounded-xl px-3 text-sm sm:col-span-2">
            <select name="manche" class="h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white">
                <option value="sans_objet">Sans objet</option>
                <option value="courte">Manche courte</option>
                <option value="longue">Manche longue</option>
            </select>
            <input type="number" step="0.01" name="prix_defaut" placeholder="Prix par défaut (FCFA)" required
                   class="h-9 border border-slate-200 rounded-xl px-3 text-sm">
            <button type="submit"
                    class="sm:col-span-4 h-9 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                    style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                Ajouter au catalogue
            </button>
        </form>
    </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Désignation</th>
                        <th class="text-left px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Manche</th>
                        <th class="text-right px-4 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Prix par défaut</th>
                        @if(Auth::user()->role === 'directeur')
                            <th class="px-4 py-3"></th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($designations as $designation)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800 text-[13px]">{{ $designation->nom }}</td>
                            <td class="px-4 py-3 text-slate-500 text-[12px] capitalize">{{ str_replace('_', ' ', $designation->manche) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-[12px]" style="color:#0C447C">{{ number_format($designation->prix_defaut, 0, ',', ' ') }} F</td>
                            @if(Auth::user()->role === 'directeur')
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('designations.destroy', $designation) }}" onsubmit="return confirm('Supprimer cette désignation ?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600 text-[11px]">Supprimer</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune désignation dans le catalogue</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>