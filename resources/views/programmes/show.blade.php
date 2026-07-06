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
    $montantTotal = $programme->montantTotal();
    $paye = $programme->montantPaye();
    $solde = $montantTotal - $paye;
    $taux = $programme->tauxPaiement();
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
                    Année scolaire {{ $programme->annee_scolaire }}
                    @if($programme->ecole->contact_nom) · {{ $programme->ecole->contact_nom }} @endif
                    @if($programme->ecole->contact_telephone) · {{ $programme->ecole->contact_telephone }} @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('programmes.statut', $programme) }}" class="flex items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="statut" onchange="this.form.submit()"
                            class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] bg-white text-slate-700">
                        <option value="en_cours" {{ $programme->statut === 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="termine" {{ $programme->statut === 'termine' ? 'selected' : '' }}>Terminé</option>
                        <option value="annule" {{ $programme->statut === 'annule' ? 'selected' : '' }}>Annulé</option>
                    </select>
                </form>
                <a href="{{ route('programmes.index') }}"
                   class="h-9 px-4 rounded-xl text-[12px] font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center">
                    ← Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Montant total</p>
                <p class="text-[18px] font-black" style="color:#0C447C">{{ number_format($montantTotal, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Encaissé</p>
                <p class="text-[18px] font-black" style="color:#3B6D11">{{ number_format($paye, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Solde restant</p>
                <p class="text-[18px] font-black" style="color:{{ $solde > 0 ? '#A32D2D' : '#3B6D11' }}">{{ number_format($solde, 0, ',', ' ') }} F</p>
            </div>
            <div class="rounded-xl p-3" style="background:#f8fafc">
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Taux payé</p>
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 rounded-full overflow-hidden" style="background:#E6F1FB">
                        <div class="h-full rounded-full" style="width:{{ $taux }}%; background:linear-gradient(90deg,#0C447C,#378ADD)"></div>
                    </div>
                    <span class="text-[13px] font-bold" style="color:#0C447C">{{ $taux }}%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         CONTRAT
    ══════════════════════════════════════ --}}
    @if($programme->contrat)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Contrat de prestation</h3>
        </div>
        <div class="p-5 space-y-3">
            @if($programme->contrat->description_engagement)
                <p class="text-[12px] text-slate-600 whitespace-pre-line">{{ $programme->contrat->description_engagement }}</p>
            @endif
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Représentant école</p>
                    <p class="text-[12px] font-semibold text-slate-700">{{ $programme->contrat->representant_client ?? '–' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Délai livraison</p>
                    <p class="text-[12px] font-semibold text-slate-700">
                        {{ $programme->contrat->date_limite_livraison?->format('d/m/Y') ?? $programme->contrat->delai_livraison_texte ?? '–' }}
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Date signature</p>
                    <p class="text-[12px] font-semibold text-slate-700">{{ $programme->contrat->date_signature?->format('d/m/Y') ?? '–' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         BONS DE COMMANDE
    ══════════════════════════════════════ --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Bons de commande</h3>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Nouveau bon de commande'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form method="POST" action="{{ route('programmes.bons.store', $programme) }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="numero" placeholder="Numéro du bon" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <select name="condition_paiement" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] bg-white">
                    @foreach(\App\Models\BonCommande::conditionsProposees() as $cp)
                        <option value="{{ $cp }}">{{ $cp }}</option>
                    @endforeach
                    <option value="">Autre / à préciser plus tard</option>
                </select>
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Créer le bon</button>
            </form>
            <p class="text-[10px] text-slate-400 mt-2">Vous ajouterez les articles (désignation, taille, couleur...) juste après, sur le bon créé.</p>
        </div>

        <div class="p-5 space-y-4">
            @forelse($programme->bonsCommande as $bon)
                <div x-data="{ openLignes: {{ session('bon_ouvert') == $bon->id ? 'true' : 'false' }}, openAjout: {{ session('bon_ouvert') == $bon->id ? 'true' : 'false' }} }"
                     class="border border-slate-100 rounded-xl overflow-hidden">

                    {{-- En-tête du bon --}}
                    <div class="flex items-center justify-between px-4 py-3 cursor-pointer" style="background:#f8fafc" @click="openLignes = !openLignes">
                        <div class="flex items-center gap-3">
                            <span class="font-mono font-bold text-[12px]" style="color:#0C447C">{{ $bon->numero }}</span>
                            <span class="text-[11px] text-slate-400">{{ $bon->date->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-slate-400">· {{ $bon->condition_paiement ?: 'Condition non précisée' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-[13px]" style="color:#185FA5">{{ number_format($bon->montant, 0, ',', ' ') }} F</span>
                            <form method="POST" action="{{ route('programmes.bons.destroy', $bon) }}" onsubmit="return confirm('Supprimer ce bon et tous ses articles ?')" @click.stop>
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 text-[11px]">Supprimer</button>
                            </form>
                        </div>
                    </div>

                    <div x-show="openLignes">
                        @if($bon->montant > 0)
                            <p class="px-4 pt-3 text-[10px] italic text-slate-400">
                                Montant en lettres : {{ \App\Support\NombreEnLettres::enMontant($bon->montant) }}
                            </p>
                        @endif

                        {{-- Tableau des lignes --}}
                        <div class="overflow-x-auto px-4 pt-2">
                            <table class="w-full text-[12px]">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Désignation</th>
                                        <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Taille</th>
                                        <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Couleur</th>
                                        <th class="text-left py-2 text-[9px] font-semibold text-slate-400 uppercase">Matière</th>
                                        <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Logo</th>
                                        <th class="text-center py-2 text-[9px] font-semibold text-slate-400 uppercase">Qté</th>
                                        <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">P.U.</th>
                                        <th class="text-right py-2 text-[9px] font-semibold text-slate-400 uppercase">Montant</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($bon->lignes as $ligne)
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
                                    @empty
                                        <tr><td colspan="9" class="py-4 text-center text-slate-400 text-[11px]">Aucun article — ajoutez-en un ci-dessous</td></tr>
                                    @endforelse
                                </tbody>
                                @if($bon->lignes->count() > 0)
                                    <tfoot>
                                        <tr style="background:#f8fafc">
                                            <td colspan="5" class="py-2 text-[11px] font-bold text-slate-600">TOTAL</td>
                                            <td class="py-2 text-center font-bold text-[12px]">{{ $bon->quantiteTotale() }}</td>
                                            <td></td>
                                            <td class="py-2 text-right font-bold text-[12px]" style="color:#0C447C">{{ number_format($bon->montant, 0, ',', ' ') }} F</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        {{-- Ajouter un article --}}
                        <div class="p-4">
                            <button type="button" @click="openAjout = !openAjout" class="text-[11px] font-semibold" style="color:#185FA5">
                                <span x-text="openAjout ? '– Fermer' : '+ Ajouter un article'"></span>
                            </button>

                            <form x-show="openAjout" method="POST" action="{{ route('programmes.bons.lignes.store', $bon) }}"
                                  x-data="ligneForm()"
                                  class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3">
                                @csrf
                                <select name="designation_id" x-model="designationId" @change="appliquerPrix()"
                                        class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] bg-white sm:col-span-2">
                                    <option value="">-- Désignation libre --</option>
                                    @foreach($designations as $d)
                                        <option value="{{ $d->id }}" data-prix="{{ $d->prix_defaut }}">{{ $d->libelle() }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="designation_libre" x-show="!designationId" placeholder="Désignation (si absente du catalogue)"
                                       class="h-8 border border-slate-200 rounded-lg px-2 text-[11px] sm:col-span-2">
                                <input type="text" name="taille" placeholder="Taille (12, M, XL...)" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                <input type="text" name="couleur" placeholder="Couleur" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                <input type="text" name="matiere" placeholder="Matière" class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                <label class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                    <input type="checkbox" name="logo" value="1" class="w-3.5 h-3.5" style="accent-color:#185FA5"> Avec logo
                                </label>
                                <input type="number" name="quantite" placeholder="Quantité" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                <input type="number" step="0.01" name="prix_unitaire" x-model="prixUnitaire" placeholder="Prix unitaire" required class="h-8 border border-slate-200 rounded-lg px-2 text-[11px]">
                                <button type="submit" class="sm:col-span-4 h-8 rounded-lg text-[11px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter l'article</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-400 text-[12px] py-6">Aucun bon de commande pour ce programme</p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════
         ÉCHÉANCIER DE PAIEMENT
    ══════════════════════════════════════ --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Échéancier de paiement (prévu)</h3>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Ajouter'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form method="POST" action="{{ route('programmes.echeances.store', $programme) }}" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @csrf
                <input type="date" name="date_prevue" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="number" step="0.01" name="montant_prevu" placeholder="Montant prévu" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Versement</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date prévue</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Montant prévu</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->echeancesPaiement as $ech)
                        <tr>
                            <td class="px-4 py-2.5 text-[12px] font-semibold text-slate-700">#{{ $ech->numero_versement }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-600">{{ $ech->date_prevue->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold text-[12px]">{{ number_format($ech->montant_prevu, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-2.5 text-right">
                                <form method="POST" action="{{ route('programmes.echeances.destroy', $ech) }}" onsubmit="return confirm('Supprimer cette échéance ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-[11px]">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucune échéance</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         PAIEMENTS (encaissements réels)
    ══════════════════════════════════════ --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Paiements encaissés</h3>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Ajouter'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form method="POST" action="{{ route('programmes.paiements.store', $programme) }}" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                @csrf
                <input type="date" name="date" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="number" step="0.01" name="montant" placeholder="Montant" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <select name="mode_paiement" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] bg-white">
                    <option value="espece">Espèces</option>
                    <option value="cheque">Chèque</option>
                    <option value="virement">Virement</option>
                    <option value="wave">Wave</option>
                    <option value="orange_money">Orange Money</option>
                    <option value="agent_mandate">Agent mandaté</option>
                </select>
                <input type="text" name="reference" placeholder="Référence (n° chèque...)" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Enregistrer</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Montant</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Mode</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Référence</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Reçu par</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->paiements as $paiement)
                        <tr>
                            <td class="px-4 py-2.5 text-[12px] text-slate-600">{{ $paiement->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold text-[12px]" style="color:#3B6D11">{{ number_format($paiement->montant, 0, ',', ' ') }} F</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-500">{{ $paiement->modeLabel() }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-500">{{ $paiement->reference ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-500">{{ $paiement->receveur->login ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <form method="POST" action="{{ route('programmes.paiements.destroy', $paiement) }}" onsubmit="return confirm('Supprimer ce paiement ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-400 hover:text-red-600 text-[11px]">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400 text-[12px]">Aucun paiement enregistré</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         FICHE DE PRODUCTION
    ══════════════════════════════════════ --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Fiche de production</h3>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Ajouter un article'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form method="POST" action="{{ route('programmes.articles.store', $programme) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="designation" placeholder="Désignation (ex : Chemise LM)" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px] sm:col-span-2">
                <input type="number" name="quantite" placeholder="Quantité" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="file" name="photo" accept="image/*" class="h-9 text-[12px]">
                <textarea name="description" placeholder="Specs (couleur, tissu, col, manches...)" rows="2" class="sm:col-span-3 border border-slate-200 rounded-xl px-3 py-2 text-[12px]"></textarea>
                <button type="submit" class="h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Ajouter</button>
            </form>
        </div>

        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4">
            @forelse($programme->articlesProduction as $article)
                <div class="border border-slate-100 rounded-xl overflow-hidden">
                    <div class="h-28 bg-slate-50 flex items-center justify-center">
                        @if($article->photoUrl())
                            <img src="{{ $article->photoUrl() }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-[10px] text-slate-300">Pas de photo</span>
                        @endif
                    </div>
                    <div class="p-2.5">
                        <p class="text-[12px] font-bold text-slate-800">{{ $article->designation }}</p>
                        @if($article->quantite)<p class="text-[10px] text-slate-400">Qté : {{ $article->quantite }}</p>@endif
                        @if($article->description)<p class="text-[10px] text-slate-400 mt-1 line-clamp-2">{{ $article->description }}</p>@endif
                        <form method="POST" action="{{ route('programmes.articles.destroy', $article) }}" onsubmit="return confirm('Supprimer cet article ?')" class="mt-2">
                            @csrf @method('DELETE')
                            <button class="text-red-400 hover:text-red-600 text-[10px]">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-400 text-[12px] py-8">Aucun article de production</p>
            @endforelse
        </div>
    </div>

    {{-- ══════════════════════════════════════
         LIVRAISONS
    ══════════════════════════════════════ --}}
    <div x-data="{ open: false }" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Livraisons</h3>
            <button @click="open = !open" class="text-[11px] font-semibold px-3 py-1.5 rounded-lg" style="color:#185FA5; background:#E6F1FB">
                <span x-text="open ? 'Fermer' : '+ Ajouter'"></span>
            </button>
        </div>

        <div x-show="open" class="p-5 border-b border-slate-100" style="background:#f8fafc">
            <form method="POST" action="{{ route('programmes.livraisons.store', $programme) }}" class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                @csrf
                <input type="date" name="date" required class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="text" name="livreur" placeholder="Livreur" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="text" name="receptionniste" placeholder="Réceptionniste" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="number" name="quantite" placeholder="Quantité livrée" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <input type="text" name="description" placeholder="Détail" class="h-9 border border-slate-200 rounded-xl px-3 text-[12px]">
                <button type="submit" class="col-span-2 sm:col-span-5 h-9 rounded-xl text-[12px] font-bold text-white" style="background:linear-gradient(135deg,#185FA5,#378ADD)">Enregistrer</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100" style="background:#f8fafc">
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Date</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Livreur</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Réceptionniste</th>
                        <th class="text-right px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Quantité</th>
                        <th class="text-left px-4 py-2.5 text-[10px] font-semibold text-slate-400 uppercase">Détail</th>
                        <th class="px-4 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($programme->livraisons as $livraison)
                        <tr>
                            <td class="px-4 py-2.5 text-[12px] text-slate-600">{{ $livraison->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-700">{{ $livraison->livreur ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-700">{{ $livraison->receptionniste ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-right text-[12px] font-semibold">{{ $livraison->quantite ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-slate-500">{{ $livraison->description ?? '–' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                <form method="POST" action="{{ route('programmes.livraisons.destroy', $livraison) }}" onsubmit="return confirm('Supprimer cette livraison ?')">
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

    {{-- ══════════════════════════════════════
         SUPPRIMER LE PROGRAMME
    ══════════════════════════════════════ --}}
    <div class="flex justify-end">
        <form method="POST" action="{{ route('programmes.destroy', $programme) }}" onsubmit="return confirm('Supprimer définitivement ce programme et toutes ses données (bons, paiements, production, livraisons) ?')">
            @csrf @method('DELETE')
            <button class="text-[11px] font-semibold text-red-400 hover:text-red-600">Supprimer ce programme</button>
        </form>
    </div>

</div>

<script>
function ligneForm() {
    return {
        designationId: '',
        prixUnitaire: '',
        appliquerPrix() {
            if (!this.designationId) return;
            const select = this.$root.querySelector('select[name="designation_id"]');
            const option = select.querySelector(`option[value="${this.designationId}"]`);
            if (option) {
                this.prixUnitaire = option.dataset.prix;
            }
        },
    };
}
</script>
</x-app-layout>