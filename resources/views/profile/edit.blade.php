<x-app-layout>
<x-slot name="title">Mon profil</x-slot>

{{-- ── Notifications flash ── --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
         x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0 -translate-y-2"
         class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#EAF3DE; color:#3B6D11; border-color:#C3E6A0">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
        {{ session('success') }}
    </div>
@endif

@if (session('error') || $errors->any())
    <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border"
         style="background:#FCEBEB; color:#A32D2D; border-color:#F5C0C0">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') ?? $errors->first() }}
    </div>
@endif

<div class="space-y-5 max-w-3xl">

    {{-- ══════════════════════════════════════
         1. INFORMATIONS PERSONNELLES
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Informations personnelles</h3>
        </div>
        <div class="p-5">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-[18px] font-bold text-white flex-shrink-0"
                     style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    {{ $user->role === 'assistant'
                        ? mb_strtoupper(substr($employe->nom ?? 'AS', 0, 2))
                        : 'DG' }}
                </div>
                <div>
                    <p class="text-[15px] font-bold text-slate-800">
                        {{ $employe ? $employe->prenom . ' ' . $employe->nom : $user->login }}
                    </p>
                    <p class="text-[11px] text-slate-400 capitalize mt-0.5">{{ $user->role }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Rôle</p>
                    <p class="text-[13px] font-semibold text-slate-700 capitalize">{{ $user->role }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Compte créé le</p>
                    <p class="text-[13px] font-semibold text-slate-700">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Dernière connexion</p>
                    <p class="text-[13px] font-semibold text-slate-700">
                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y à H:i') : '—' }}
                    </p>
                </div>
            </div>

            {{-- Modifier le login --}}
            <form method="POST" action="{{ route('profile.login') }}" class="flex items-end gap-3 pt-4 border-t border-slate-100">
                @csrf
                @method('PUT')
                <div class="flex-1 max-w-xs">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Login</label>
                    <input type="text" name="login" value="{{ old('login', $user->login) }}" required
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>
                <button type="submit"
                        class="h-9 px-4 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Enregistrer
                </button>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════
         EMPLOYÉ LIÉ (assistant uniquement, lecture seule)
    ══════════════════════════════════════ --}}
    @if($employe)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Fiche employé liée</h3>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Matricule</p>
                <p class="text-[13px] font-mono font-bold" style="color:#0C447C">{{ $employe->matricule }}</p>
            </div>
            
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Département</p>
                <p class="text-[13px] font-semibold text-slate-700">{{ $employe->departement ?? '–' }}</p>
            </div>
            <div>
                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Date d'embauche</p>
                <p class="text-[13px] font-semibold text-slate-700">
                    {{ $employe->date_embauche ? $employe->date_embauche->format('d/m/Y') : '–' }}
                </p>
            </div>
        </div>
        <p class="px-5 pb-4 text-[10px] text-slate-400">Ces informations sont gérées par le directeur depuis la fiche employé.</p>
    </div>
    @endif

    {{-- ══════════════════════════════════════
         2. SÉCURITÉ
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">Sécurité</h3>
        </div>
        <form method="POST" action="{{ route('profile.password') }}" class="p-5 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Mot de passe actuel *</label>
                    <input type="password" name="current_password" required
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Nouveau mot de passe *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Confirmer *</label>
                    <input type="password" name="password_confirmation" required minlength="6"
                           class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit"
                        class="h-9 px-5 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════
         3. PRÉFÉRENCES (à venir)
    ══════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 opacity-60">
        <h3 class="text-[12px] font-semibold text-slate-800 mb-1">Préférences</h3>
        <p class="text-[11px] text-slate-400">Langue, thème et notifications arrivent dans une prochaine mise à jour.</p>
    </div>

</div>
</x-app-layout>