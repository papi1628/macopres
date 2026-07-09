<x-app-layout>
    <x-slot name="title">Nouvelle commande client</x-slot>

    <div x-data="{ ecoleMode: 'existante' }" class="space-y-5 max-w-3xl">

    <form method="POST" action="{{ route('programmes.store') }}">
    @csrf


    {{-- ══════════════════════════════════════
        CLIENT / ECOLE
    ══════════════════════════════════════ --}}

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">

        <div class="px-5 py-3.5 border-b border-slate-100">
            <h3 class="text-[12px] font-semibold text-slate-800">
                École / Client
            </h3>
        </div>


        <div class="p-5 space-y-4">


            <div class="flex gap-2">

                <button type="button"
                    @click="ecoleMode='existante'"
                    class="px-4 py-2 rounded-xl text-[12px] font-semibold"
                    :class="ecoleMode==='existante'
                    ? 'text-white'
                    : 'text-slate-500 border border-slate-200'"
                    :style="ecoleMode==='existante'
                    ? 'background:linear-gradient(135deg,#185FA5,#378ADD)'
                    : ''">

                    École existante

                </button>



                <button type="button"
                    @click="ecoleMode='nouvelle'"
                    class="px-4 py-2 rounded-xl text-[12px] font-semibold"
                    :class="ecoleMode==='nouvelle'
                    ? 'text-white'
                    : 'text-slate-500 border border-slate-200'"
                    :style="ecoleMode==='nouvelle'
                    ? 'background:linear-gradient(135deg,#185FA5,#378ADD)'
                    : ''">

                    Nouvelle école

                </button>

            </div>



            {{-- Ecole existante --}}

            <div x-show="ecoleMode==='existante'">

                <label class="block text-[10px] uppercase font-semibold text-slate-500 mb-1">
                    Choisir l'école
                </label>


                <select name="ecole_id"
                        class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm bg-white">

                    <option value="">
                        -- Sélectionner --
                    </option>


                    @foreach($ecoles as $ecole)

                        <option value="{{ $ecole->id }}">
                            {{ $ecole->nom }}
                        </option>

                    @endforeach


                </select>

            </div>



            {{-- Nouvelle école --}}

            <div x-show="ecoleMode==='nouvelle'"
                class="grid grid-cols-1 sm:grid-cols-2 gap-4">


                <div>
                    <label class="label">
                        Nom de l'école *
                    </label>

                    <input type="text"
                        name="ecole_nom"
                        class="input">

                </div>



                <div>
                    <label class="label">
                        Adresse
                    </label>

                    <input type="text"
                        name="ecole_adresse"
                        class="input">

                </div>



                <div>
                    <label class="label">
                        Téléphone
                    </label>

                    <input type="text"
                        name="ecole_telephone"
                        class="input">

                </div>



                <div>
                    <label class="label">
                        Responsable
                    </label>

                    <input type="text"
                        name="ecole_contact_nom"
                        class="input">

                </div>



                <div>
                    <label class="label">
                        Téléphone responsable
                    </label>

                    <input type="text"
                        name="ecole_contact_telephone"
                        class="input">

                </div>


            </div>


        </div>

    </div>




    {{-- ══════════════════════════════════════
        COMMANDE
    ══════════════════════════════════════ --}}


    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-5">


    <div class="px-5 py-3.5 border-b border-slate-100">

    <h3 class="text-[12px] font-semibold text-slate-800">
    Commande
    </h3>

    </div>



    <div class="p-5 space-y-4">



    <div>

    <label class="block text-[10px] uppercase font-semibold text-slate-500 mb-1">
    Nature de la commande
    </label>


    <input type="text"
        name="nature"
        placeholder="Ex : Uniformes scolaires "
        required
        class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm">

    </div>




    <div class="max-w-xs">

    <label class="block text-[10px] uppercase font-semibold text-slate-500 mb-1">
    Année scolaire
    </label>


    <input type="text"
        name="annee_scolaire"
        placeholder="2025/2026"
        required
        class="w-full h-9 border border-slate-200 rounded-xl px-3 text-sm">

    </div>



    </div>


    </div>





    <div class="flex justify-end gap-3">


        <a href="{{route('programmes.index')}}"
            class="h-10 px-5 rounded-xl border border-slate-200 text-slate-500 text-sm flex items-center">

                Annuler

        </a>



        <button type="submit"
            class="h-10 px-6 rounded-xl text-white text-sm font-bold"
            style="background:linear-gradient(135deg,#0C447C,#185FA5)">

                Enregistrer
        </button>


    </div>



    </form>

    </div>



    <style>

    .label{
        display:block;
        font-size:10px;
        font-weight:600;
        text-transform:uppercase;
        color:#64748b;
        margin-bottom:6px;
    }


    .input{
        width:100%;
        height:36px;
        border:1px solid #e2e8f0;
        border-radius:12px;
        padding:0 12px;
        font-size:14px;
    }

    </style>


</x-app-layout>