<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Assistants</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 p-6">

    <div class="max-w-5xl mx-auto">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold">
                Assistants
            </h1>

            <a href="/assistants/create"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Ajouter
            </a>

        </div>

        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-200">

                    <tr>
                        <th class="p-4 text-left">ID</th>
                        <th class="p-4 text-left">Login</th>
                        <th class="p-4 text-left">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($assistants as $assistant)

                        <tr class="border-t">

                            <td class="p-4">
                                {{ $assistant->id }}
                            </td>

                            <td class="p-4">
                                {{ $assistant->login }}
                            </td>

                            <td class="p-4 flex gap-2">

                                <a href="/assistants/{{ $assistant->id }}/edit"
                                   class="bg-yellow-500 text-white px-3 py-1 rounded">
                                    Modifier
                                </a>

                                <form method="POST"
                                      action="/assistants/{{ $assistant->id }}">

                                    @csrf
                                    @method('DELETE')

                                    <button class="bg-red-600 text-white px-3 py-1 rounded">
                                        Supprimer
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>