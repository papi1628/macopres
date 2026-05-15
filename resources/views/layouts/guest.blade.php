<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MACOPRES') }} — Connexion</title>

        <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo-macopres.svg') }}?v=1">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }

            .bg-grid {
                background-image:
                    linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
                background-size: 40px 40px;
            }

            .glow-1 {
                background: radial-gradient(ellipse 80% 60% at 70% 30%, rgba(24,95,165,.35) 0%, transparent 60%);
            }
            .glow-2 {
                background: radial-gradient(ellipse 60% 80% at 20% 80%, rgba(12,68,124,.5) 0%, transparent 55%);
            }
            .glow-3 {
                background: radial-gradient(ellipse 40% 40% at 85% 75%, rgba(55,138,221,.15) 0%, transparent 50%);
            }

            .card-glass {
                background: rgba(255,255,255,.06);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 2px solid rgba(255,255,255,.1);
                box-shadow: 0 32px 64px rgba(4,44,83,.4), inset 0 1px 0 rgba(255,255,255,.08);
            }

            .logo-glow {
                box-shadow: 0 0 0 1px rgba(255,255,255,.1), 0 20px 40px rgba(4,44,83,.5), 0 0 60px rgba(55,138,221,.25);
            }

            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-up { animation: fadeUp .45s ease-out both; }

            @keyframes spin { to { transform: rotate(360deg); } }
            .animate-spin-fast { animation: spin .6s linear infinite; }
        </style>
    </head>

    <body class="min-h-screen antialiased overflow-hidden relative" style="background-color:#042C53;">

        {{-- Couches de fond --}}
        <div class="glow-1 fixed inset-0 pointer-events-none z-0"></div>
        <div class="glow-2 fixed inset-0 pointer-events-none z-0"></div>
        <div class="glow-3 fixed inset-0 pointer-events-none z-0"></div>
        <div class="bg-grid fixed inset-0 pointer-events-none z-0"></div>

        {{-- Cercles décoratifs --}}
        <div class="fixed -top-52 -right-36 w-[600px] h-[600px] rounded-full pointer-events-none z-0"
             style="border:2px solid rgba(55,138,221,.08)"></div>
        <div class="fixed -top-28 -right-12 w-[400px] h-[400px] rounded-full pointer-events-none z-0"
             style="border:2px solid rgba(55,138,221,.12)"></div>
        <div class="fixed -bottom-96 -left-48 w-[800px] h-[800px] rounded-full pointer-events-none z-0"
             style="border:2px solid rgba(55,138,221,.06)"></div>

        {{-- Contenu centré --}}
        <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 py-10">
            {{ $slot }}
        </div>

    </body>
</html>