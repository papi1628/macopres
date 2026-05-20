<x-app-layout>

<x-slot name="title">
    Calendrier RH
</x-slot>

<div class="space-y-5">

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">

        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-[18px] font-bold text-slate-800">
                    Calendrier RH
                </h1>

                <p class="text-sm text-slate-400 mt-1">
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
    <div class="grid grid-cols-7 gap-3">

        @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $jourNom)

            <div class="text-center text-xs font-bold text-slate-400 uppercase">
                {{ $jourNom }}
            </div>

        @endforeach

        @foreach($jours as $jour)

            <div class="min-h-[120px] rounded-2xl border border-slate-100 bg-white p-3 shadow-sm
                {{ $jour['weekend'] ? 'bg-slate-50' : '' }}">

                <div class="flex items-center justify-between">

                    <span class="text-sm font-bold text-slate-700">
                        {{ $jour['date']->day }}
                    </span>

                    @if($jour['weekend'])

                        <span class="text-[9px] px-2 py-1 rounded-full bg-slate-200 text-slate-600">
                            Repos
                        </span>

                    @endif

                </div>

            </div>

        @endforeach

    </div>

</div>

</x-app-layout>