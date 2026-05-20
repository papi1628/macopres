@php
    $active = fn($r) => request()->routeIs($r) ? true : false;

    $sections = [
        [
            'label' => 'Principal',
            'links' => [
                [
                    'label' => 'Tableau de bord',
                    'route' => 'dashboard.directeur',
                    'icon'  => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
                ],
            ],
        ],
        [
            'label' => 'Employés',
            'links' => [
                [
                    'label' => 'Tous les employés',
                    'route' => 'employes.index',
                    'icon'  => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
                ],
                [
                    'label' => 'Assistants',
                    'role'  => 'directeur',
                    'route' => 'assistants.index',
                    'icon'  => 'M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z',
                ],
            ],
        ],
        [
            'label' => 'Pointage',
            'links' => [
                [
                    'label' => 'Feuille de présence',
                    'route' => 'pointages.index',
                    'icon'  => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
                ],
                [
                    'label' => 'Scanner QR',
                    'route' => 'pointages.scan',
                    'icon'  => 'M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z',
                ],
                [
                    'label' => 'Historique',
                    'route' => 'pointages.historique',
                    'icon'  => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 9v7.5',
                ],
                [
                    'label' => 'Statistiques',
                    'route' => 'pointages.statistiques',
                    'icon'  => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
                ],
                [
                    'label' => 'Calendrier',
                    'route' => 'calendrier.index',
                    'icon'  => 'M8.25 3v1.5M15.75 3v1.5M3 8.25h18M4.5 5.25h15A1.5 1.5 0 0121 6.75v12A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75v-12A1.5 1.5 0 014.5 5.25z',
                ],
            ],
        ],
        /*[
            'label' => 'Administration',
            'role'  => 'directeur',   // ← section visible que par le directeur
            'links' => [
                [
                    'label' => 'Rôles',
                    'route' => 'roles.index',
                    'icon'  => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z',
                ],
            ],
        ],*/
    ];
@endphp

@foreach ($sections as $section)

    {{-- Cacher la section Admin aux assistants --}}
    @if (isset($section['role']) && Auth::user()->role !== $section['role'])
        @continue
    @endif

    {{-- Séparateur (sauf pour la première section) --}}
    @if (!$loop->first)
        <div class="mx-3 my-2 border-t" style="border-color:rgba(255,255,255,.1)"></div>
    @endif

    {{-- Label de section — non cliquable --}}
    <div class="px-4 py-1" :class="{ 'lg:hidden': sidebarCollapsed }">
        <p class="text-[9px] font-bold uppercase tracking-[.12em] select-none pointer-events-none"
           style="color:rgba(255,255,255,.3)">{{ $section['label'] }}</p>
    </div>

    {{-- Liens --}}
    @foreach ($section['links'] as $link)
        @if (isset($link['role']) && Auth::user()->role !== $link['role'])
            @continue
        @endif
        @if (Route::has($link['route']))
        <a href="{{ route($link['route']) }}"
           class="sidebar-link group flex items-center gap-3 mx-2 px-2.5 py-[8px] rounded-lg mb-[2px] transition-all duration-150
                  {{ $active($link['route']) ? 'active' : '' }}"
           title="{{ $link['label'] }}">

            <svg class="nav-icon w-[15px] h-[15px] flex-shrink-0 transition-opacity"
                 fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"/>
            </svg>

            <span class="text-[12px] whitespace-nowrap overflow-hidden transition-all duration-300 leading-tight
                         {{ $active($link['route']) ? 'font-semibold text-white' : 'font-normal group-hover:text-white' }}"
                  :class="{ 'lg:max-w-0 lg:opacity-0': sidebarCollapsed, 'max-w-[160px] opacity-100': !sidebarCollapsed }">
                {{ $link['label'] }}
            </span>
        </a>
        @endif
    @endforeach

@endforeach