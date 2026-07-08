<x-app-layout>
<x-slot name="title">{{ $programme->ecole->nom }} — {{ $programme->annee_scolaire }}</x-slot>

@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        {{ session('success') }}
    </div>
@endif

@php
    $nbCommandes = $programme->bonsCommande->count();
    $montantTotal = $programme->bonsCommande->sum('montant');
    $articlesCommandes = $programme->bonsCommande->sum(fn($b) => $b->lignes->sum('quantite'));
    $statutStyle = [
        'en_cours' => ['En cours', '#DBEAFE', '#1D4ED8'],
        'termine'  => ['Terminé', '#EAF3DE', '#3B6D11'],
        'annule'   => ['Annulé', '#FCEBEB', '#A32D2D'],
    ][$programme->statut];
@endphp

<div class="space-y-5">

    {{-- ══════════════════════════════════════
         EN-TÊTE
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between flex-wrap gap-4 mb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-[17px] font-bold text-slate-800">{{ $programme->ecole->nom }}</h2>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:{{ $statutStyle[1] }}; color:{{ $statutStyle[2] }}">{{ $statutStyle[0] }}</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    Programme {{ $programme->annee_scolaire }} · Créé le {{ $programme->created_at->format('d/m/Y') }}
                </p>
            </div>
            <a href="{{ route('programmes.index') }}"
               class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                ← Retour
            </a>
        </div>

        {{-- Résumé --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Nombre de commandes</p>
                <p class="text-[18px] font-black" style="color:#0C447C">{{ $nbCommandes }}</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Montant total commandé</p>
                <p class="text-[18px] font-black" style="color:#0C447C">{{ number_format($montantTotal, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Articles commandés</p>
                <p class="text-[18px] font-black" style="color:#0C447C">{{ $articlesCommandes }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         COMMANDES CLIENT
    ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between">
        <h3 class="text-[13px] font-bold text-slate-800">Commandes client</h3>
        @if($nbCommandes !== 0)
        <form method="POST" action="{{ route('programmes.bons.store', $programme) }}">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                    style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nouvelle commande
            </button>
        </form>
        @endif
    </div>

    @if($nbCommandes === 0)
        {{-- État vide --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-12 text-center">
            <p class="text-[14px] font-semibold text-slate-600">Aucune commande enregistrée</p>
            <p class="text-[12px] text-slate-400 mt-1">Le client n'a pas encore passé de commande.</p>
            <form method="POST" action="{{ route('programmes.bons.store', $programme) }}" class="inline-block mt-4">
                @csrf
                <button type="submit"
                        class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    + Nouvelle commande
                </button>
            </form>
        </div>
    @else
        {{-- Timeline des commandes --}}
        <div class="relative pl-8">
            <div class="absolute left-[15px] top-2 bottom-2 w-px" style="background:#E2E8F0"></div>

            @foreach($programme->bonsCommande as $i => $bon)
                <div x-data="{ open: {{ session('bon_ouvert') == $bon->id ? 'true' : 'false' }} }" class="relative mb-5">

                    {{-- Numéro de la timeline --}}
                    <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-[11px] font-black text-white"
                         style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                        {{ $i + 1 }}
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                        {{-- En-tête du BC --}}
                        <div class="flex items-center justify-between px-5 py-4 cursor-pointer" @click="open = !open">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-400">Commande n°{{ $i + 1 }}</p>
                                <p class="font-mono font-bold text-[14px]" style="color:#0C447C">{{ $bon->numero }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-6 text-right">
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Date</p>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $bon->date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Montant</p>
                                    <p class="text-[13px] font-bold" style="color:#185FA5">{{ number_format($bon->montant, 0, ',', ' ') }} F</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase">Articles</p>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $bon->lignes->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div x-show="open" class="border-t border-slate-100">
                            {{-- Infos complémentaires --}}
                            <div class="px-5 py-3 grid grid-cols-2 sm:grid-cols-3 gap-4" style="background:#f8fafc">
                                <div>
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase mb-1">Nature</p>
                                    <p class="text-[12px] text-slate-700">{{ $bon->nature ?? '–' }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px]  font-semibold text-slate-400 uppercase mb-1">Condition de paiement</p>
                                    <form method="POST" action="{{ route('programmes.bons.condition', $bon) }}">
                                        @csrf @method('PATCH')
                                        <select name="condition_paiement" onchange="this.form.submit()"
                                                class="h-7 border border-slate-200 rounded-lg px-2 py-1 text-[11px] bg-white text-slate-700">
                                            <option value="" {{ !$bon->condition_paiement ? 'selected' : '' }}>–</option>
                                            @foreach(\App\Models\BonCommande::conditionsProposees() as $cp)
                                                <option value="{{ $cp }}" {{ $bon->condition_paiement === $cp ? 'selected' : '' }}>{{ $cp }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                @if($bon->montant > 0)
                                <div class="col-span-2 sm:col-span-1">
                                    <p class="text-[9px] font-semibold text-slate-400 uppercase mb-1">Montant en lettres</p>
                                    <p class="text-[11px] italic text-slate-500">{{ \App\Support\NombreEnLettres::enMontant($bon->montant) }}</p>
                                </div>
                                @endif
                            </div>

                            {{-- Articles --}}
                            <div class="px-5 py-4">
                                <p class="text-[11px] font-bold text-slate-700 mb-2">Articles</p>

                                @if($bon->lignes->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-[12px]">
                                            <thead>
                                                <tr class="border-b border-slate-100">
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Désignation</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Taille / Classe</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Couleur</th>
                                                    <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Matière</th>
                                                    <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Logo</th>
                                                    <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Quantité</th>
                                                    <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">P.U</th>
                                                    <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">Total</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-50">
                                                @foreach($bon->lignes as $ligne)
                                                    <tr>
                                                        <td class="py-2 font-semibold text-slate-700">{{ $ligne->libelle() }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->taille ?? '–' }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->couleur ?? '–' }}</td>
                                                        <td class="py-2 text-slate-500">{{ $ligne->matiere ?? '–' }}</td>
                                                        <td class="py-2 text-center">{{ $ligne->logo ? '✓' : '–' }}</td>
                                                        <td class="py-2 text-center font-semibold">{{ $ligne->quantite }}</td>
                                                        <td class="py-2 text-right">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }}</td>
                                                        <td class="py-2 text-right font-semibold" style="color:#185FA5">{{ number_format($ligne->montant_ligne, 0, ',', ' ') }}</td>
                                                        <td class="py-2 text-right">
                                                            <form method="POST" action="{{ route('programmes.bons.lignes.destroy', $ligne) }}" onsubmit="return confirm('Supprimer cet article ?')">
                                                                @csrf @method('DELETE')
                                                                <button class="text-red-400 hover:text-red-600 text-[10px]">&times;</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="background:#f8fafc">
                                                    <td colspan="7" class="py-2 text-[11px] font-bold text-slate-600 text-right">TOTAL</td>
                                                    <td class="py-2 text-right font-bold text-[12px]" style="color:#0C447C">{{ number_format($bon->montant, 0, ',', ' ') }} F</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-[12px] text-slate-400 text-center py-4">0 article — ajoutez-en un ci-dessous</p>
                                @endif

                                {{-- Ajouter un article --}}
                                <div x-data="{ openAjout: {{ $bon->lignes->count() === 0 ? 'true' : 'false' }}, ...ligneForm() }" class="mt-3">
                                    <button type="button" @click="openAjout = !openAjout" class="text-[11px] font-semibold" style="color:#185FA5">
                                        <span x-text="openAjout ? '– Fermer' : '+ Ajouter un article'"></span>
                                    </button>

                                    <form x-show="openAjout" method="POST" action="{{ route('programmes.bons.lignes.store', $bon) }}"
                                          class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                                        @csrf
                                        {{-- <select name="designation_id" x-model="designationId" @change="appliquerPrix($event)"
                                                class="h-8 border border-slate-200 rounded-lg px-2 py-1 text-[11px] bg-white sm:col-span-2">
                                            <option value="">-- Choisir une désignation --</option>
                                            @foreach($designations as $d)
                                                <option value="{{ $d->id }}" data-prix="{{ $d->prix_defaut }}">{{ $d->libelle() }}</option>
                                            @endforeach
                                            <option value="">Désignation libre (hors catalogue)</option>
                                        </select> --}}
                                        <input type="text" name="designation_libre" x-show="!designationId" placeholder="Nom de l'article"
                                               class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] sm:col-span-2">
                                        <input type="text" name="taille" placeholder="Taille (12, M, XL...) / Classe (CI, 6e, Terminale...)" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="number" name="quantite" placeholder="Quantité" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="text" name="couleur" placeholder="Couleur" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="text" name="matiere" placeholder="Matière" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <input type="number" step="0.01" name="prix_unitaire" x-model="prixUnitaire" placeholder="Prix unitaire" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                        <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                            <input type="checkbox" name="logo" value="1" class="w-3.5 h-3.5" style="accent-color:#185FA5"> Avec logo
                                        </label>
                                        
                                        <button type="submit" class="sm:col-span-4 h-8 rounded-lg text-[11px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter l'article</button>
                                    </form>
                                </div>
                            </div>

                            <div class="px-5 pb-4">
                                <form method="POST" action="{{ route('programmes.bons.destroy', $bon) }}" onsubmit="return confirm('Supprimer cette commande et tous ses articles ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-[10px] font-semibold text-red-400 hover:text-red-600">Supprimer cette commande</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

<script>
function ligneForm() {
    return {
        designationId: '',
        prixUnitaire: '',
        appliquerPrix(event) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            this.prixUnitaire = option?.dataset?.prix ?? '';
        },
    };
}
</script>
</x-app-layout>