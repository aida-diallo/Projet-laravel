<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @filamentStyles
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header>
            <x-filament::header />
        </header>
        
        <main class="flex-1">
            {{ $slot }}
        </main>

        <footer>
            <x-filament::footer />
        </footer>
    </div>
    @filamentScripts
</body>
</html>
