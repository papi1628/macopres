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
                <p class="text-[18px] font-black" style="color:#0C447C">{{ $programme->bonsCommande->count() }}</p>
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
         PROGRAMME CLIENT
    ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between">
        <h3 class="text-[13px] font-bold text-slate-800">Programme</h3>
        
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
                    + Bon de commande
                </button>
            </form>
        </div>
    @else
        {{-- ══════════════════════════════════════
            TIMELINE — 3 SECTIONS
        ══════════════════════════════════════ --}}
        <div class="relative pl-8">
            <div class="absolute left-[15px] top-2 bottom-2 w-px" style="background:#E2E8F0"></div>

            {{-- SECTION BON DE COMMANDE --}}
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 2.25H7.5A2.25 2.25 0 0 0 5.25 4.5v15A2.25 2.25 0 0 0 7.5 21.75h9a2.25 2.25 0 0 0 2.25-2.25V4.5A2.25 2.25 0 0 0 16.5 2.25H15M9 2.25v2.25h6V2.25M9 2.25a1.5 1.5 0 0 0 1.5 1.5h3a1.5 1.5 0 0 0 1.5-1.5M9.75 12h4.5m-4.5 3.75h4.5"/>
                    </svg>
                </div>

                <a href="{{ route('programmes.bons.index', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#DBEAFE; color:#1D4ED8">Bon de commande</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $programme->bonsCommande->count() }} {{ Str::plural('commande', $nbCommandes) }}
                            </p>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Montant total</p>
                                <p class="text-[13px] font-bold" style="color:#185FA5">{{ number_format($programme->bonsCommande->sum('montant'),0,',',' ') }} F</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- SECTION CONTRAT --}}
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>

                <a href="{{ route('programmes.contrat.show', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#F3E8FF; color:#7E22CE">Contrat</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $programme->contrat ? 'Voir le contrat' : 'Aucun contrat renseigné' }}
                            </p>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Montant</p>
                                <p class="text-[13px] font-bold" style="color:#185FA5">{{ number_format($programme->contrat?->bonCommande?->montant ?? 0,0,',',' ') }} F</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- SECTION FACTURES --}}
            @php
                $nbFactures = $programme->bonsCommande->filter(fn($b) => $b->facture)->count();
                $montantFacture = $programme->bonsCommande->sum(fn($b) => $b->facture->montant ?? 0);
            @endphp
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z"/>
                    </svg>
                </div>
            
                <a href="{{ route('programmes.factures.index', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#EAF3DE; color:#3B6D11">Facture</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $nbFactures }} {{ Str::plural('facture', $nbFactures) }}
                            </p>
                        </div>
            
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Total facturé</p>
                                <p class="text-[13px] font-bold" style="color:#185FA5">{{ number_format($montantFacture, 0, ',', ' ') }} F</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- SECTION RELEVÉ DE COMPTE --}}
            @php
                $totalFacturesReleve = $programme->bonsCommande->sum(fn($b) => $b->facture->montant ?? 0);
                $totalVerse = $programme->paiements->sum('montant');
            @endphp
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM4.5 10.5h.008v.008H4.5V10.5zm15 0h.008v.008h-.008V10.5z"/>
                    </svg>
                </div>
            
                <a href="{{ route('programmes.releve.show', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#FEF6E4; color:#854F0B">Relevé de compte</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $programme->paiements->count() }} {{ Str::plural('versement', $programme->paiements->count()) }}
                            </p>
                        </div>
            
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Reste à payer</p>
                                <p class="text-[13px] font-bold" style="color:{{ ($totalFacturesReleve - $totalVerse) > 0 ? '#A32D2D' : '#3B6D11' }}">
                                    {{ number_format($totalFacturesReleve - $totalVerse, 0, ',', ' ') }} F
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

            {{-- SECTION FICHES DE PRODUCTION --}}
            @php
                $nbFiches = $programme->bonsCommande->filter(fn($b) => $b->lignes->count() > 0)->count();
                $articlesAProduire = $programme->bonsCommande->sum(fn($b) => $b->lignes->sum('quantite'));
            @endphp
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#0C447C,#185FA5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                    </svg>
                </div>
            
                <a href="{{ route('programmes.fiches.index', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#F3E8FF; color:#7E22CE">Fiche de production</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $nbFiches }} {{ Str::plural('fiche', $nbFiches) }}
                            </p>
                        </div>
            
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Articles à produire</p>
                                <p class="text-[13px] font-bold" style="color:#185FA5">{{ $articlesAProduire }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
            {{-- SECTION LIVRAISONS --}}
            @php
                $nbLivraisons = $programme->livraisons->count();
                $piecesLivrees = $programme->livraisons->sum('quantite');
                $piecesCommandees = $programme->bonsCommande->sum(fn($b) => $b->lignes->sum('quantite'));
            @endphp
            <div class="relative mb-5">
                <div class="absolute -left-8 top-4 w-8 h-8 rounded-full flex items-center justify-center text-white"
                    style="background:linear-gradient(135deg,#166534,#3B6D11)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
            
                <a href="{{ route('programmes.livraisons.index', $programme) }}"
                class="block bg-white rounded-2xl border border-slate-100 shadow-sm px-5 py-4 transition-all hover:-translate-y-px hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:#DBFCE7; color:#166534">Livraisons</span>
                            <p class="font-bold text-[14px] text-slate-700">
                                {{ $nbLivraisons }} {{ Str::plural('livraison', $nbLivraisons) }}
                            </p>
                        </div>
            
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <p class="text-[9px] font-semibold text-slate-400 uppercase">Livré / Commandé</p>
                                <p class="text-[13px] font-bold" style="color:{{ $piecesLivrees >= $piecesCommandees && $piecesCommandees > 0 ? '#3B6D11' : '#185FA5' }}">
                                    {{ $piecesLivrees }} / {{ $piecesCommandees }}
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>

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