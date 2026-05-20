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

    <div class="flex items-center gap-2">

        <button
            onclick="openCreateModal()"
            class="h-10 px-4 rounded-xl text-white text-sm font-semibold shadow-sm"
            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
            + Ajouter un événement
        </button>

    </div>

    {{-- CALENDRIER --}}
    <div class="grid grid-cols-7 gap-3">

        {{-- JOURS --}}
        @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $jourNom)

            <div class="text-center text-xs font-bold text-slate-400 uppercase py-2">
                {{ $jourNom }}
            </div>

        @endforeach

        @foreach($jours as $jour)

            @php
                $isToday = $jour['date']->isToday();
            @endphp

            <button
                type="button"
                onclick="openEventModal('{{ $jour['date']->format('Y-m-d') }}')"

                class="min-h-[120px] rounded-2xl border p-3 shadow-sm transition-all hover:shadow-md text-left relative

                @if(!$jour['dans_mois'])
                    bg-slate-50 border-slate-100 opacity-40

                @elseif($isToday)
                    border-[#185FA5] ring-2 ring-blue-100 bg-blue-50

                @elseif($jour['weekend'])
                    bg-slate-100 border-slate-200

                @else
                    bg-white border-slate-100
                @endif
            ">

                {{-- AUJOURD’HUI --}}
                @if($isToday)
                    <span class="absolute top-2 right-2 text-[9px] px-2 py-0.5 rounded-full bg-[#185FA5] text-white font-bold">
                        Aujourd’hui
                    </span>
                @endif

                {{-- JOUR --}}
                <div class="flex items-start justify-between">

                    <span class="text-sm font-bold text-slate-700">
                        {{ $jour['date']->day }}
                    </span>

                </div>

                {{-- EVENEMENTS --}}
                <div class="mt-2 flex flex-col gap-1">

                    @foreach($jour['evenements'] ?? [] as $event)

                        <div class="text-[10px] px-2 py-1 rounded-full font-semibold truncate"
                            style="background: {{ $event->badge['bg'] }};
                                color: {{ $event->badge['color'] }}">

                            {{ $event->titre }}

                        </div>

                    @endforeach

                    {{-- WEEK-END --}}
                    @if($jour['weekend'])

                        <div class="text-[9px] px-2 py-1 rounded-full bg-slate-200 text-slate-600 inline-block w-fit">
                            Week-end
                        </div>

                    @endif

                </div>

            </button>

        @endforeach

    </div>

</div>

{{-- MODAL --}}
<div id="eventModal"
     class="hidden fixed inset-0 z-50 bg-black/40 items-center justify-center p-4">

    <div class="bg-white rounded-2xl w-full max-w-xl p-5 shadow-xl">

        <div class="flex items-center justify-between mb-5">

            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Gestion des événements
                </h2>

                <p id="modalDate"
                   class="text-sm text-slate-400 mt-1">
                </p>
            </div>

            <button onclick="closeEventModal()"
                    class="w-9 h-9 rounded-xl hover:bg-slate-100 text-slate-500">
                ✕
            </button>

        </div>

        {{-- EVENEMENTS --}}
        <div id="eventsList"
             class="space-y-2 mb-5">
        </div>

        {{-- FORMULAIRE --}}
        <form method="POST"
              action="{{ route('calendrier.store') }}"
              class="space-y-4">

            @csrf

            <input type="hidden"
                   name="date"
                   id="eventDateInput">

            <div>
                <label class="text-xs font-semibold text-slate-500 uppercase">
                    Type
                </label>

                <select name="type"
                        class="w-full mt-1 rounded-xl border-slate-200 text-sm">

                    <option value="ferie">Jour férié</option>
                    <option value="repos">Repos</option>
                    <option value="evenement">Événement</option>

                </select>
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500 uppercase">
                    Titre
                </label>

                <input type="text"
                       name="titre"
                       required
                       class="w-full mt-1 rounded-xl border-slate-200 text-sm">
            </div>

            <div>
                <label class="text-xs font-semibold text-slate-500 uppercase">
                    Description
                </label>

                <textarea name="description"
                          rows="3"
                          class="w-full mt-1 rounded-xl border-slate-200 text-sm"></textarea>
            </div>

            <label class="flex items-center gap-2">

                <input type="checkbox"
                       name="est_paye"
                       value="1"
                       class="rounded border-slate-300">

                <span class="text-sm text-slate-600">
                    Journée payée
                </span>

            </label>

            <div class="flex justify-end gap-2 pt-2">

                <button type="button"
                        onclick="closeEventModal()"
                        class="h-10 px-4 rounded-xl border border-slate-200 text-sm">
                    Annuler
                </button>

                <button type="submit"
                        class="h-10 px-4 rounded-xl text-white text-sm font-semibold"
                        style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                    Enregistrer
                </button>

            </div>

        </form>

    </div>

</div>

<script>

async function openEventModal(date)
{
    const modal = document.getElementById('eventModal');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('eventDateInput').value = date;

    const response = await fetch(`/calendrier/${date}`);

    const data = await response.json();

    document.getElementById('modalDate').innerText =
        data.date;

    let html = '';

    if (data.evenements.length === 0) {

        html = `
            <div class="text-sm text-slate-400 bg-slate-50 rounded-xl p-3">
                Aucun événement
            </div>
        `;

    } else {

        data.evenements.forEach(event => {

            html += `
                <div class="rounded-xl border border-slate-100 p-3">

                    <div class="flex items-center justify-between">

                        <div>
                            <p class="text-sm font-semibold text-slate-700">
                                ${event.titre}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                ${event.type}
                            </p>
                        </div>

                        ${event.est_paye
                            ? `<span class="text-[10px] px-2 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                    Payé
                               </span>`
                            : ''
                        }

                    </div>

                </div>
            `;
        });
    }

    document.getElementById('eventsList').innerHTML = html;
}

function closeEventModal()
{
    const modal = document.getElementById('eventModal');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openCreateModal()
{
    openEventModal(
        '{{ now()->format('Y-m-d') }}'
    );
}

</script>

</x-app-layout>