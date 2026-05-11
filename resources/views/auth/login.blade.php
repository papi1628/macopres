<x-guest-layout>

    <div class="w-full max-w-[420px] animate-fade-up">

        {{-- ── Brand ── --}}
        <div class="text-center mb-7">
            <div class="logo-glow inline-flex items-center justify-center w-16 h-16 rounded-[18px] text-3xl font-black text-white mb-4"
                 style="background: linear-gradient(135deg, #185FA5, #378ADD);">
                M
            </div>
            <h1 class="text-[26px] font-extrabold text-white tracking-tight leading-none">MACOPRES</h1>
            <p class="text-[12px] text-blue-200 mt-1.5 font-normal">Système de gestion RH &amp; Pointage</p>
        </div>

        {{-- ── Carte ── --}}
        <div class="card-glass rounded-[20px] p-8">

            <h2 class="text-[18px] font-bold text-white mb-1">Connexion</h2>
            <p class="text-[13px] text-blue-200 mb-6">Bienvenue, identifiez-vous pour continuer</p>

            {{-- Status session --}}
            <x-auth-session-status :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Identifiant --}}
                <div class="mb-4">
                    <x-input-label for="login" :value="__('Identifiant')" />
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-200 text-[15px] pointer-events-none select-none">👤</span>
                        <x-text-input
                            id="login"
                            name="login"
                            type="text"
                            :value="old('login')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Entrez votre identifiant"
                            class="!pl-11 {{ $errors->has('login') ? '!border-red-400/60 !ring-red-400/20' : '' }}"
                        />
                    </div>
                    <x-input-error :messages="$errors->get('login')" />
                </div>

                {{-- Mot de passe --}}
                <div class="mb-5">
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-200 text-[15px] pointer-events-none select-none">🔒</span>
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="!pl-11 !pr-12 {{ $errors->has('password') ? '!border-red-400/60 !ring-red-400/20' : '' }}"
                        />
                        <button
                            type="button"
                            id="eyeBtn"
                            onclick="togglePassword()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-blue-200 hover:text-white transition-colors text-base leading-none"
                            aria-label="Afficher / masquer"
                        >👁</button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                {{-- Se souvenir --}}
                <div class="flex items-center gap-2 mb-6">
                    <input
                        id="remember_me"
                        type="checkbox"
                        name="remember"
                        class="w-4 h-4 rounded cursor-pointer flex-shrink-0"
                        style="accent-color:#378ADD"
                    >
                    <label for="remember_me" class="text-[12px] text-blue-200 cursor-pointer select-none">
                        Se souvenir de moi
                    </label>
                </div>

                {{-- Bouton submit --}}
                <x-primary-button id="submitBtn">
                    <span id="btnText" class="flex items-center justify-center gap-2">
                        Se connecter
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </span>
                    <span id="spinner" class="hidden">
                        <svg class="animate-spin w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="3"/>
                            <path class="opacity-90" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </span>
                </x-primary-button>

            </form>

            {{-- Comptes démo --}}
            <!--
            <div class="mt-6 pt-5" style="border-top:1px solid rgba(255,255,255,.08)">
                <p class="text-[10px] font-semibold text-center uppercase tracking-[.1em] mb-3"
                   style="color:rgba(255,255,255,.3)">
                    Comptes disponibles — cliquez pour pré-remplir
                </p>
                <div class="grid grid-cols-2 gap-2">

                    <button type="button" id="card-directeur"
                        onclick="fillDemo('directeur','1234','directeur')"
                        class="rounded-xl px-3 py-3 text-center transition-all duration-150 hover:-translate-y-px focus:outline-none">
                        <div id="av-directeur" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white mx-auto mb-2"
                             style="background:linear-gradient(135deg,#185FA5,#378ADD)">KA</div>
                        <div class="text-[11px] font-semibold text-white whitespace-nowrap">Koné Amadou</div>
                        <div class="text-[10px] text-blue-100 mt-0.5">Directeur</div>
                    </button>

                    <button type="button" id="card-assistant"
                        onclick="fillDemo('assistant','1234','assistant')"
                        class="rounded-xl px-3 py-3 text-center transition-all duration-150 hover:-translate-y-px focus:outline-none">
                        <div id="av-assistant" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold mx-auto mb-2"
                             style="background:rgba(255,255,255,.1);color:#94a3b8">DF</div>
                        <div class="text-[11px] font-semibold text-white whitespace-nowrap">Diallo Fatimata</div>
                        <div class="text-[10px] text-blue-200 mt-0.5">Assistant</div>
                    </button>

                </div>
            </div>
            -->

        </div>

        {{-- Pied de page --}}
        <p class="text-center text-[11px] mt-5" style="color:rgba(255,255,255,.2)">
            MACOPRES GROUP &nbsp;·&nbsp; Dakar, Sénégal
        </p>

    </div>

    <script>
        // Pré-remplissage
        function fillDemo(login, password, role) {
            document.getElementById('login').value    = login;
            document.getElementById('password').value = password;

            ['directeur','assistant'].forEach(r => {
                const card = document.getElementById('card-' + r);
                const av   = document.getElementById('av-' + r);
                const active = (r === role);

                card.style.background = active ? 'rgba(24,95,165,.25)' : 'rgba(255,255,255,.04)';
                card.style.border     = active ? '1px solid rgba(55,138,221,.5)' : '1px solid rgba(255,255,255,.08)';
                av.style.background   = active ? 'linear-gradient(135deg,#185FA5,#378ADD)' : 'rgba(255,255,255,.1)';
                av.style.color        = active ? '#fff' : '#94a3b8';
            });
        }

        // Toggle mot de passe
        function togglePassword() {
            const input     = document.getElementById('password');
            const btn       = document.getElementById('eyeBtn');
            input.type      = input.type === 'password' ? 'text' : 'password';
            btn.textContent = input.type === 'password' ? '👁' : '🙈';
        }

        // Loader au submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn     = document.getElementById('submitBtn');
            const txtEl   = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            btn.disabled = true;
            txtEl.classList.add('hidden');
            spinner.classList.remove('hidden');
        });

        
    </script>

</x-guest-layout>