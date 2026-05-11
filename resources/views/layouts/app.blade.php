<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MACOPRES') }} — {{ $title ?? 'Tableau de bord' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', sans-serif; }

            /* Lien actif sidebar */
            .sidebar-link { color: rgba(255,255,255,.6); }
            .sidebar-link:hover { background: rgba(255,255,255,.08); color: #fff; }
            .sidebar-link.active { background: rgba(255,255,255,.14); color: #fff; }
            .sidebar-link.active .nav-icon { opacity: 1; }
            .sidebar-link .nav-icon { opacity: .6; }
            .sidebar-link:hover .nav-icon { opacity: 1; }
        </style>
    </head>

    {{--
        x-data sur body :
        - sidebarOpen  : overlay mobile (false par défaut)
        - sidebarCollapsed : réduit sur desktop (false par défaut)
    --}}
    <body
        class="font-sans antialiased"
        style="background:#f0f4f8"
        x-data="{ sidebarOpen: false, sidebarCollapsed: false }"
        @keydown.escape="sidebarOpen = false"
    >

        <div class="flex h-screen overflow-hidden">

            {{-- ══════════════════════════════════════
                 OVERLAY MOBILE (fond sombre)
            ══════════════════════════════════════ --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black/50 lg:hidden"
                style="display:none"
            ></div>

            {{-- ══════════════════════════════════════
                 SIDEBAR
            ══════════════════════════════════════ --}}
            <aside
                :class="{
                    'translate-x-0': sidebarOpen,
                    '-translate-x-full': !sidebarOpen,
                    'lg:translate-x-0': true,
                    'lg:w-[64px]': sidebarCollapsed,
                    'lg:w-[220px]': !sidebarCollapsed,
                    'w-[220px]': true
                }"
                class="fixed inset-y-0 left-0 z-30 flex flex-col transition-all duration-300 ease-in-out lg:relative lg:flex lg:flex-shrink-0"
                style="background:#0C447C;"
            >
                {{-- ── Header sidebar ── --}}
                <div class="flex items-center gap-3 px-4 border-b flex-shrink-0"
                     style="border-color:rgba(255,255,255,.1); height:52px;">

                    {{-- Logo --}}
                    <div class="w-[30px] h-[30px] rounded-lg flex items-center justify-center font-black text-white text-[14px] flex-shrink-0"
                         style="background:#185FA5;">M</div>

                    {{-- Nom (caché quand collapsed sur desktop) --}}
                    <div class="flex-1 min-w-0 lg:block"
                         :class="{ 'lg:hidden': sidebarCollapsed }">
                        <p class="text-white font-bold text-[13px] leading-tight whitespace-nowrap">MACOPRES</p>
                        <p class="text-[10px] whitespace-nowrap" style="color:#B5D4F4;">RH &amp; Pointage</p>
                    </div>

                    {{-- Bouton collapse DESKTOP — toujours visible --}}
                    <button
                        @click="sidebarCollapsed = !sidebarCollapsed"
                        class="hidden lg:flex w-6 h-6 items-center justify-center rounded-md transition-colors hover:bg-white/10 focus:outline-none flex-shrink-0"
                        :title="sidebarCollapsed ? 'Agrandir le menu' : 'Réduire le menu'"
                    >
                        {{-- Flèche gauche = réduire --}}
                        <svg x-show="!sidebarCollapsed" class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{-- Flèche droite = agrandir --}}
                        <svg x-show="sidebarCollapsed" class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Bouton fermer MOBILE --}}
                    <button
                        @click="sidebarOpen = false"
                        class="lg:hidden w-6 h-6 flex items-center justify-center rounded-md transition-colors hover:bg-white/10 focus:outline-none flex-shrink-0"
                    >
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- ── Navigation ── --}}
                <nav class="flex-1 py-3 overflow-y-auto overflow-x-hidden">
                    @include('layouts.navigation')
                </nav>

                {{-- ── Footer utilisateur ── --}}
                <div class="flex-shrink-0 px-2 py-3 border-t" style="border-color:rgba(255,255,255,.1)">
                    <div class="flex items-center gap-2.5 px-2 py-2 rounded-lg" style="background:rgba(255,255,255,.06)">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                             style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                            {{ Auth::user()->role === 'assistant'
    ? strtoupper(substr(Auth::user()->employeAssistant->nom ?? 'AS', 0, 2))
    : 'DG'
}}
                        </div>
                        <div class="flex-1 min-w-0" :class="{ 'lg:hidden': sidebarCollapsed }">
                             
                            <p class="text-[9px] capitalize" style="color:#85B7EB">{{ Auth::user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" :class="{ 'lg:hidden': sidebarCollapsed }">
                            @csrf
                            <button type="submit"
                                    class="w-6 h-6 flex items-center justify-center rounded-md transition-colors hover:bg-red-500/20 focus:outline-none flex-shrink-0"
                                    title="Déconnexion">
                                <svg class="w-3.5 h-3.5 text-blue-200 hover:text-red-300 transition-colors" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- ══════════════════════════════════════
                 ZONE PRINCIPALE
            ══════════════════════════════════════ --}}
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                {{-- Top bar --}}
                <header class="flex-shrink-0 flex items-center justify-between px-4 sm:px-6 bg-white border-b border-slate-100 shadow-sm"
                        style="height:52px;">

                    <div class="flex items-center gap-3">
                        {{-- Hamburger MOBILE --}}
                        <button
                            @click="sidebarOpen = true"
                            class="lg:hidden w-8 h-8 flex items-center justify-center rounded-lg transition-colors hover:bg-slate-100 focus:outline-none"
                        >
                            <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Titre de page --}}
                        <h1 class="text-[14px] font-bold text-slate-800 truncate">
                            {{ $title ?? 'Tableau de bord' }}
                        </h1>
                    </div>

                    {{-- Droite --}}
                    <div class="flex items-center gap-2 sm:gap-4">

                        {{-- Date (cachée sur très petit écran) --}}
                        <span class="hidden md:block text-[11px] text-slate-400 font-medium whitespace-nowrap">
                            {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMM YYYY') }}
                        </span>

                        {{-- Bouton Scanner QR --}}
                        <a href="#"
                           class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl text-[11px] font-bold text-white transition-all hover:-translate-y-px whitespace-nowrap"
                           style="background:linear-gradient(135deg,#185FA5,#378ADD); box-shadow:0 4px 12px rgba(12,68,124,.3)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                            </svg>
                            Scanner QR
                        </a>

                        {{-- Avatar + dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.outside="open = false"
                                    class="flex items-center gap-2 px-2 py-1.5 rounded-xl transition-colors hover:bg-slate-50 focus:outline-none">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0"
                                     style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                                    {{ Auth::user()->role === 'assistant'
    ? strtoupper(substr(Auth::user()->employeAssistant->nom ?? 'AS', 0, 2))
    : 'DG'
}}
                                </div>
                                <div class="hidden sm:block text-left">
                                    
                                    <p class="text-[10px] text-slate-400 leading-tight capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <svg class="w-3 h-3 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute right-0 top-full mt-1 w-44 bg-white rounded-xl border border-slate-100 shadow-xl py-1 z-50"
                                 style="display:none">
                                <div class="px-4 py-2.5 border-b border-slate-100">
                                    <p class="text-[12px] font-semibold text-slate-700">{{ Auth::user()->role === 'assistant'
    ? strtoupper(substr(Auth::user()->employeAssistant->nom ?? 'AS', 0, 2))
    : 'DG'
}}</p>
                                    <p class="text-[10px] text-slate-400 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2.5 text-[12px] text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                        </svg>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Contenu scrollable --}}
                <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                    {{ $slot }}
                </main>

            </div>
        </div>

    </body>
</html>