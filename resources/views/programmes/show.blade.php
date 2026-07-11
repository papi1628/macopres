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