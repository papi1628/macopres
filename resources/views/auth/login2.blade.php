<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MACOPRES — Connexion</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue-900: #042C53;
            --blue-800: #0C447C;
            --blue-600: #185FA5;
            --blue-400: #378ADD;
            --blue-200: #85B7EB;
            --blue-100: #B5D4F4;
            --blue-50:  #E6F1FB;
            --white:    #FFFFFF;
            --slate-50: #F8FAFC;
            --slate-200:#E2E8F0;
            --slate-400:#94A3B8;
            --slate-600:#475569;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--blue-900);
            overflow: hidden;
            position: relative;
        }

        /* ── Fond animé ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 70% 30%, rgba(24, 95, 165, 0.35) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 20% 80%, rgba(12, 68, 124, 0.5) 0%, transparent 55%),
                radial-gradient(ellipse 40% 40% at 85% 75%, rgba(55, 138, 221, 0.15) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Grille décorative ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* ── Cercles décoratifs ── */
        .deco {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }
        .deco-1 {
            width: 600px; height: 600px;
            top: -200px; right: -150px;
            border: 1px solid rgba(55,138,221,.08);
        }
        .deco-2 {
            width: 400px; height: 400px;
            top: -100px; right: -50px;
            border: 1px solid rgba(55,138,221,.12);
        }
        .deco-3 {
            width: 800px; height: 800px;
            bottom: -400px; left: -200px;
            border: 1px solid rgba(55,138,221,.06);
        }

        /* ── Wrapper principal ── */
        .wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 24px;
            animation: fadeUp .5s ease-out both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Logo / Brand ── */
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-400));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -1px;
            margin-bottom: 14px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.1),
                0 20px 40px rgba(4,44,83,.5),
                0 0 60px rgba(55,138,221,.2);
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -.5px;
            line-height: 1;
        }

        .brand-tagline {
            font-size: 12px;
            color: var(--blue-200);
            margin-top: 5px;
            font-weight: 400;
            letter-spacing: .01em;
        }

        /* ── Carte connexion ── */
        .card {
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
            padding: 32px;
            box-shadow:
                0 32px 64px rgba(4,44,83,.4),
                inset 0 1px 0 rgba(255,255,255,.08);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 4px;
        }

        .card-sub {
            font-size: 13px;
            color: var(--blue-200);
            margin-bottom: 24px;
        }

        /* ── Alerte session ── */
        .alert-success {
            background: rgba(59, 109, 17, .2);
            border: 1px solid rgba(59, 109, 17, .4);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            color: #86efac;
            margin-bottom: 16px;
        }

        /* ── Champs ── */
        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--blue-100);
            text-transform: uppercase;
            letter-spacing: .07em;
            margin-bottom: 7px;
        }

        .field-wrap {
            position: relative;
        }

        .field-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--blue-200);
            font-size: 15px;
            pointer-events: none;
            z-index: 1;
        }

        .field input {
            width: 100%;
            height: 46px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 11px;
            padding: 0 14px 0 42px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            color: #fff;
            transition: border-color .2s, background .2s, box-shadow .2s;
            -webkit-appearance: none;
        }

        .field input::placeholder {
            color: rgba(255,255,255,.25);
        }

        .field input:focus {
            outline: none;
            border-color: var(--blue-400);
            background: rgba(255,255,255,.1);
            box-shadow: 0 0 0 3px rgba(55,138,221,.2);
        }

        .field input.error {
            border-color: rgba(248, 113, 113, .6);
            box-shadow: 0 0 0 3px rgba(248,113,113,.15);
        }

        /* ── Eye toggle ── */
        .eye-btn {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--blue-200);
            font-size: 16px;
            padding: 0;
            line-height: 1;
            transition: color .2s;
        }
        .eye-btn:hover { color: #fff; }

        /* ── Erreur ── */
        .field-error {
            font-size: 11px;
            color: #fca5a5;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── Remember ── */
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--blue-400);
            cursor: pointer;
            flex-shrink: 0;
        }

        .remember label {
            font-size: 12px;
            color: var(--blue-200);
            cursor: pointer;
        }

        /* ── Bouton principal ── */
        .btn-login {
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-400));
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            letter-spacing: .02em;
            position: relative;
            overflow: hidden;
            transition: transform .15s, box-shadow .2s;
            box-shadow: 0 8px 24px rgba(12,68,124,.5), 0 0 0 1px rgba(255,255,255,.08);
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,.12), transparent);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 32px rgba(12,68,124,.6), 0 0 0 1px rgba(255,255,255,.1);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        /* ── Rôles démo ── */
        .demo-section {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .demo-title {
            font-size: 10px;
            font-weight: 600;
            color: rgba(255,255,255,.3);
            text-transform: uppercase;
            letter-spacing: .1em;
            text-align: center;
            margin-bottom: 12px;
        }

        .demo-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .demo-card {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 10px;
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            transition: background .2s, border-color .2s, transform .15s;
        }

        .demo-card:hover {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.15);
            transform: translateY(-1px);
        }

        .demo-card.active {
            background: rgba(24,95,165,.25);
            border-color: rgba(55,138,221,.5);
        }

        .demo-av {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 7px;
            background: linear-gradient(135deg, var(--blue-600), var(--blue-400));
        }

        .demo-card:not(.active) .demo-av {
            background: rgba(255,255,255,.1);
        }

        .demo-name {
            font-size: 11px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
        }

        .demo-role {
            font-size: 10px;
            color: var(--blue-200);
            margin-top: 2px;
        }

        .demo-card.active .demo-role {
            color: var(--blue-100);
        }

        /* ── Pied de page ── */
        .footer {
            text-align: center;
            margin-top: 22px;
            font-size: 11px;
            color: rgba(255,255,255,.2);
        }

        /* ── Loader sur submit ── */
        .spinner {
            display: none;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

    {{-- Décoration --}}
    <div class="deco deco-1"></div>
    <div class="deco deco-2"></div>
    <div class="deco deco-3"></div>

    <div class="wrapper">

        {{-- Brand --}}
        <div class="brand">
            <div class="brand-logo">M</div>
            <div class="brand-name">MACOPRES</div>
            <div class="brand-tagline">Système de gestion RH &amp; Pointage</div>
        </div>

        {{-- Carte --}}
        <div class="card">
            <div class="card-title">Connexion</div>
            <div class="card-sub">Bienvenue, identifiez-vous pour continuer</div>

            {{-- Status session --}}
            @if (session('status'))
                <div class="alert-success">✓ {{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                {{-- Identifiant --}}
                <div class="field">
                    <label for="login">Identifiant</label>
                    <div class="field-wrap">
                        <span class="field-icon">👤</span>
                        <input
                            id="login"
                            type="text"
                            name="login"
                            value="{{ old('login') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Entrez votre identifiant"
                            class="{{ $errors->has('login') ? 'error' : '' }}"
                        >
                    </div>
                    @error('login')
                        <div class="field-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="field">
                    <label for="password">Mot de passe</label>
                    <div class="field-wrap">
                        <span class="field-icon">🔒</span>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="{{ $errors->has('password') ? 'error' : '' }}"
                        >
                        <button type="button" class="eye-btn" onclick="togglePassword()" id="eyeBtn" aria-label="Afficher/masquer le mot de passe">
                            👁
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Se souvenir --}}
                <div class="remember">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Se souvenir de moi</label>
                </div>

                {{-- Bouton --}}
                <button type="submit" class="btn-login" id="submitBtn">
                    <span id="btnText">Se connecter</span>
                    <div class="spinner" id="spinner"></div>
                </button>
            </form>

            {{-- Comptes démo --}}
            <div class="demo-section">
                <div class="demo-title">Comptes disponibles — cliquez pour pré-remplir</div>
                <div class="demo-cards">
                    <div class="demo-card active" id="card-dir" onclick="fillDemo('directeur', '1234', this)">
                        <div class="demo-av">KA</div>
                        <div class="demo-name">Koné Amadou</div>
                        <div class="demo-role">Directeur</div>
                    </div>
                    <div class="demo-card" id="card-asst" onclick="fillDemo('assistant', '1234', this)">
                        <div class="demo-av">DF</div>
                        <div class="demo-name">Diallo Fatimata</div>
                        <div class="demo-role">Assistant</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pied de page --}}
        <div class="footer">
            MACOPRES &nbsp;·&nbsp; Confection industrielle de tenues scolaires &nbsp;·&nbsp; Dakar, Sénégal
        </div>

    </div>

    <script>
        // ── Pré-remplissage démo ──
        function fillDemo(login, password, el) {
            document.getElementById('login').value    = login;
            document.getElementById('password').value = password;
            document.querySelectorAll('.demo-card').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
        }

        // ── Afficher / masquer mot de passe ──
        function togglePassword() {
            const input  = document.getElementById('password');
            const btn    = document.getElementById('eyeBtn');
            const hidden = input.type === 'password';
            input.type   = hidden ? 'text' : 'password';
            btn.textContent = hidden ? '🙈' : '👁';
        }

        // ── Loader au submit ──
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn     = document.getElementById('submitBtn');
            const txt     = document.getElementById('btnText');
            const spinner = document.getElementById('spinner');
            btn.disabled  = true;
            txt.style.display    = 'none';
            spinner.style.display = 'block';
        });

        // ── Pré-remplir avec le directeur par défaut ──
        window.addEventListener('DOMContentLoaded', function() {
            @if (!old('login'))
                fillDemo('directeur', '1234', document.getElementById('card-dir'));
            @endif
        });
    </script>

</body>
</html>