@props(['empresa'])

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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|poppins:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-display antialiased bg-night text-white" x-data="{ menuAberto: false }">

    <!-- ===== HEADER ===== -->
    <header class="fixed top-0 inset-x-0 z-40 bg-night/80 backdrop-blur border-b border-night-border">
        <div class="max-w-7xl mx-auto px-6 h-18 py-3 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-10 w-auto">
            </a>

            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-white/70">
                <a href="#home" class="hover:text-gold transition-colors">Home</a>
                <a href="{{ route('estoque') }}" class="hover:text-gold transition-colors">Estoque</a>
                <a href="#venda-seu-carro" class="hover:text-gold transition-colors">Venda seu Carro</a>
                <a href="#financiamento" class="hover:text-gold transition-colors">Financiamento</a>
                <a href="#contato" class="hover:text-gold transition-colors">Contato</a>
            </nav>

            <div class="hidden lg:flex items-center gap-3 shrink-0">
                @if ($empresa?->whatsappUrl())
                    <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors">
                        WhatsApp
                    </a>
                @endif
            </div>

            <button @click="menuAberto = !menuAberto" type="button" aria-label="Menu" class="lg:hidden text-white p-2">
                <x-heroicon-o-bars-3 x-show="!menuAberto" class="w-6 h-6" />
                <x-heroicon-o-x-mark x-show="menuAberto" class="w-6 h-6" style="display:none" />
            </button>
        </div>

        <div x-show="menuAberto" x-transition @click.outside="menuAberto = false"
             class="lg:hidden border-t border-night-border bg-night px-6 py-4 space-y-3" style="display:none">
            <a href="#home" class="block text-sm text-white/80 hover:text-gold">Home</a>
            <a href="{{ route('estoque') }}" class="block text-sm text-white/80 hover:text-gold">Estoque</a>
            <a href="#venda-seu-carro" class="block text-sm text-white/80 hover:text-gold">Venda seu Carro</a>
            <a href="#financiamento" class="block text-sm text-white/80 hover:text-gold">Financiamento</a>
            <a href="#contato" class="block text-sm text-white/80 hover:text-gold">Contato</a>
            @if ($empresa?->whatsappUrl())
                <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-gold text-night font-semibold text-sm">
                    WhatsApp
                </a>
            @endif
        </div>
    </header>

    <main class="pt-[72px]">
        {{ $slot }}
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-night-soft border-t border-night-border mt-20">
        <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-9 w-auto mb-3">
                <p class="text-sm text-white/50">{{ $empresa->endereco ?? 'Endereço a configurar' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white mb-3">Contato</h4>
                @if ($empresa?->telefone)
                    <p class="text-sm text-white/50 mb-1">
                        <a href="tel:{{ $empresa->telefone }}" class="hover:text-gold">{{ $empresa->telefone }}</a>
                    </p>
                @endif
                @if ($empresa?->email_contato)
                    <p class="text-sm text-white/50">
                        <a href="mailto:{{ $empresa->email_contato }}" class="hover:text-gold">{{ $empresa->email_contato }}</a>
                    </p>
                @endif
                @if (! $empresa?->telefone && ! $empresa?->email_contato)
                    <p class="text-sm text-white/30 italic">Configure o contato em Configurações</p>
                @endif
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white mb-3">Redes Sociais</h4>
                <div class="flex flex-col gap-1">
                    @if ($empresa?->facebook_url)
                        <a href="{{ $empresa->facebook_url }}" target="_blank" rel="noopener" class="text-sm text-white/50 hover:text-gold">Facebook</a>
                    @endif
                    @if ($empresa?->instagram_url)
                        <a href="{{ $empresa->instagram_url }}" target="_blank" rel="noopener" class="text-sm text-white/50 hover:text-gold">Instagram</a>
                    @endif
                    @if (! $empresa?->facebook_url && ! $empresa?->instagram_url)
                        <p class="text-sm text-white/30 italic">A configurar</p>
                    @endif
                </div>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-white mb-3">Horário</h4>
                <p class="text-sm text-white/50">{{ $empresa->horario_funcionamento ?? 'A configurar' }}</p>
            </div>
        </div>

        <div class="border-t border-night-border">
            <div class="max-w-7xl mx-auto px-6 py-5 text-center">
                <p class="text-xs text-white/40">&copy; {{ date('Y') }} {{ $empresa->nome ?? config('app.name') }} - Todos os direitos reservados</p>
            </div>
        </div>
    </footer>

    <!-- ===== WHATSAPP FLUTUANTE ===== -->
    @if ($empresa?->whatsappUrl())
        <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-gold text-night flex items-center justify-center shadow-lg hover:bg-gold-dark transition-colors"
           title="Fale conosco via WhatsApp">
            <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
        </a>
    @endif

    @livewireScripts
</body>
</html>
