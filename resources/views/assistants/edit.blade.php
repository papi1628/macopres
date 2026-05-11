<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Modifier Assistant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-6">
        Modifier Assistant
    </h1>

    <form method="POST"
          action="/assistants/{{ $assistant->id }}">

        @csrf
        @method('PUT')

        <div class="mb-4">

            <label class="block mb-2">
                Login
            </label>

            <input type="text"
                   name="login"
                   value="{{ $assistant->login }}"
                   class="w-full border rounded-lg p-2">

        </div>

        <button class="bg-yellow-500 text-white px-4 py-2 rounded-lg">
            Modifier
        </button>

    </form>

</div>

</body>
</html>