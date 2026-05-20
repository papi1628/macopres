<x-app-layout>

<x-slot name="title">
    Calendrier RH
</x-slot>

<div class="space-y-5">

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-lg text-slate-400 mt-1">
                    {{ $date->locale('fr')->translatedFormat('F Y') }}
                </p>
            </div>

            <div class="flex gap-2">

                <a href="{{ route('calendrier.index', [
                    'mois' => $date->copy()->subMonth()->month,
                    'annee' => $date->copy()->subMonth()->year
                ]) }}"
                class="h-10 px-4 rounded-xl border border-slate-200 text-sm flex items-center">
                    ←
                </a>

                <a href="{{ route('calendrier.index', [
                    'mois' => $date->copy()->addMonth()->month,
                    'annee' => $date->copy()->addMonth()->year
                ]) }}"
                class="h-10 px-4 rounded-xl border border-slate-200 text-sm flex items-center">
                    →
                </a>

            </div>

        </div>

    </div>

    {{-- CALENDRIER --}}
<div class="space-y-3">

    {{-- BARRE DES JOURS --}}
    <div class="grid grid-cols-7 gap-3">

        @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $jourNom)

            <div class="text-center text-[11px] font-bold text-slate-400 uppercase">
                {{ $jourNom }}
            </div>

        @endforeach

    </div>

    {{-- CASES DU CALENDRIER --}}
        <div class="grid grid-cols-7 gap-3">

            @foreach($jours as $jour)

                <div class="min-h-[120px] rounded-2xl border p-3 shadow-sm transition-all hover:shadow-md

                    @if(!$jour['dans_mois'])
                        bg-slate-50 border-slate-100 opacity-40

                    @elseif($jour['weekend'])
                        bg-slate-100 border-slate-200

                    @else
                        bg-white border-slate-100
                    @endif
                ">

                    {{-- JOUR --}}
                    <div class="flex items-start justify-between">

                        <span class="text-sm font-bold text-slate-700">
                            {{ $jour['date']->day }}
                        </span>

                    </div>

                    {{-- ÉVÉNEMENTS --}}
                    <div class="mt-2 space-y-1">

                        @foreach($jour['evenements'] ?? [] as $event)

                            <div
                                class="text-[10px] px-2 py-1 rounded-full font-semibold inline-block"
                                style="background: {{ $event->badge['bg'] }};
                                    color: {{ $event->badge['color'] }}">

                                {{ $event->titre }}

                            </div>

                        @endforeach

                        {{-- WEEK-END --}}
                        @if($jour['weekend'])

                            <div class="text-[9px] px-2 py-1 rounded-full bg-slate-200 text-slate-600 inline-block">
                                Week-end
                            </div>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    </div>

</div>

</x-app-layout>