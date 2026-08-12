<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>
    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-bg text-text-primary">
    <header class="border-b border-border">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="h-9 w-auto">
            </a>
            <nav class="hidden sm:flex items-center gap-6 text-sm font-medium text-text-secondary">
                <a href="{{ route('home') }}" class="hover:text-text-primary">Início</a>
                <a href="{{ route('estoque') }}" class="hover:text-text-primary">Estoque</a>
            </nav>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-border mt-16">
        <div class="max-w-7xl mx-auto px-6 py-8 text-sm text-text-secondary">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
