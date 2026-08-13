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
    @php $empresa = app()->bound('tenant') ? app('tenant') : null; @endphp

    <header class="border-b border-border sticky top-0 bg-bg/90 backdrop-blur z-30">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="shrink-0">
                <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-9 w-auto">
            </a>
            <nav class="hidden sm:flex items-center gap-6 text-sm font-medium text-text-secondary">
                <a href="{{ route('home') }}" class="hover:text-primary">Início</a>
                <a href="{{ route('estoque') }}" class="hover:text-primary">Estoque</a>
            </nav>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('cliente.dashboard') }}" title="Área do Cliente"
                   class="hidden sm:inline-flex items-center gap-2 px-3.5 py-2 rounded-control border border-border text-text-secondary text-sm font-medium hover:border-primary hover:text-primary transition-colors">
                    <x-heroicon-o-user class="w-4 h-4" /> Área do Cliente
                </a>
                @if ($empresa?->whatsappUrl())
                    <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light transition-colors">
                        WhatsApp
                    </a>
                @endif
                <a href="{{ route('login') }}" title="Administração"
                   class="w-9 h-9 flex items-center justify-center rounded-control border border-border text-text-secondary hover:border-primary hover:text-primary transition-colors">
                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                </a>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="border-t border-border mt-16 bg-surface">
        <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-8 w-auto mb-3">
                <p class="text-sm text-text-secondary">{{ $empresa->endereco ?? '' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-text-primary mb-3">Contato</h4>
                @if ($empresa?->telefone)
                    <p class="text-sm text-text-secondary mb-1"><a href="tel:{{ $empresa->telefone }}" class="hover:text-primary">{{ $empresa->telefone }}</a></p>
                @endif
                @if ($empresa?->email_contato)
                    <p class="text-sm text-text-secondary"><a href="mailto:{{ $empresa->email_contato }}" class="hover:text-primary">{{ $empresa->email_contato }}</a></p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-semibold text-text-primary mb-3">Redes Sociais</h4>
                @if ($empresa?->facebook_url)
                    <a href="{{ $empresa->facebook_url }}" target="_blank" rel="noopener" class="block text-sm text-text-secondary hover:text-primary">Facebook</a>
                @endif
                @if ($empresa?->instagram_url)
                    <a href="{{ $empresa->instagram_url }}" target="_blank" rel="noopener" class="block text-sm text-text-secondary hover:text-primary">Instagram</a>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-semibold text-text-primary mb-3">Horário</h4>
                <p class="text-sm text-text-secondary">{{ $empresa->horario_funcionamento ?? '' }}</p>
            </div>
        </div>
        <div class="border-t border-border">
            <div class="max-w-7xl mx-auto px-6 py-5 text-center text-xs text-text-secondary">
                &copy; {{ date('Y') }} {{ $empresa->nome ?? config('app.name') }}
            </div>
        </div>
    </footer>

    @if ($empresa?->whatsappUrl())
        <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center shadow-lg hover:bg-primary-light transition-colors"
           title="Fale conosco via WhatsApp">
            <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
        </a>
    @endif

    @livewireScripts
</body>
</html>
