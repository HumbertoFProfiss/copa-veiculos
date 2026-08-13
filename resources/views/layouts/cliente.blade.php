<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Área do Cliente' }} - {{ app('tenant')?->nome ?? config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|fraunces:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-poppins antialiased bg-brand-100/40 text-ink min-h-screen flex flex-col">

    <header class="bg-white border-b border-brand-100">
        <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="{{ app('tenant')?->nome }}" class="h-9 w-auto">
            </a>
            @auth('cliente')
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-muted hidden sm:inline">Olá, {{ explode(' ', Auth::guard('cliente')->user()->nome)[0] }}</span>
                    <form method="POST" action="{{ route('cliente.logout') }}">
                        @csrf
                        <button type="submit" class="text-muted hover:text-brand-700">Sair</button>
                    </form>
                </div>
            @else
                <a href="{{ route('home') }}" class="text-sm text-muted hover:text-brand-700">Voltar ao site</a>
            @endauth
        </div>
    </header>

    @auth('cliente')
        <div class="bg-white border-b border-brand-100">
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
                           {{ $rotaAtual === $rota ? 'border-brand-700 text-brand-700' : 'border-transparent text-muted hover:text-brand-900' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endauth

    <main class="flex-1 max-w-5xl mx-auto px-6 py-12 w-full">
        {{ $slot }}
    </main>

    <footer class="border-t border-brand-100 mt-auto bg-white">
        <div class="max-w-5xl mx-auto px-6 py-6 text-center text-xs text-muted">
            &copy; {{ date('Y') }} {{ app('tenant')?->nome ?? config('app.name') }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
