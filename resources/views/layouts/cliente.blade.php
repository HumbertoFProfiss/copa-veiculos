<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Área do Cliente' }} - {{ app('tenant')?->nome ?? config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-display antialiased bg-night text-white min-h-screen flex flex-col">

    <header class="border-b border-night-border">
        <div class="max-w-5xl mx-auto px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="{{ app('tenant')?->nome }}" class="h-9 w-auto">
            </a>
            @auth('cliente')
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-white/50 hidden sm:inline">Olá, {{ explode(' ', Auth::guard('cliente')->user()->nome)[0] }}</span>
                    <form method="POST" action="{{ route('cliente.logout') }}">
                        @csrf
                        <button type="submit" class="text-white/60 hover:text-gold">Sair</button>
                    </form>
                </div>
            @else
                <a href="{{ route('home') }}" class="text-sm text-white/60 hover:text-gold">Voltar ao site</a>
            @endauth
        </div>
    </header>

    @auth('cliente')
        <div class="border-b border-night-border bg-night-soft">
            <nav class="max-w-5xl mx-auto px-6 flex items-center gap-1 overflow-x-auto">
                @php $rotaAtual = request()->route()->getName(); @endphp
                @foreach ([
                    'cliente.dashboard' => 'Perfil',
                    'cliente.favoritos' => 'Favoritos',
                    'cliente.compras' => 'Compras',
                    'cliente.garantias' => 'Garantias',
                ] as $rota => $label)
                    <a href="{{ route($rota) }}"
                       class="px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                           {{ $rotaAtual === $rota ? 'border-gold text-gold' : 'border-transparent text-white/60 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endauth

    <main class="flex-1 max-w-5xl mx-auto px-6 py-10 w-full">
        {{ $slot }}
    </main>

    <footer class="border-t border-night-border mt-auto">
        <div class="max-w-5xl mx-auto px-6 py-6 text-center text-xs text-white/30">
            &copy; {{ date('Y') }} {{ app('tenant')?->nome ?? config('app.name') }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
