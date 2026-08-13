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
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|fraunces:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-poppins antialiased bg-white text-ink">
    @php $empresa = app()->bound('tenant') ? app('tenant') : null; @endphp

    @php $heroTransparente = $heroTransparente ?? false; @endphp

    <div x-data="{ scrolled: {{ $heroTransparente ? 'false' : 'true' }} }"
         x-init="@if($heroTransparente) scrolled = window.scrollY > 24; window.addEventListener('scroll', () => scrolled = window.scrollY > 24) @endif">
        <header :class="scrolled ? 'bg-white/90 shadow-sm backdrop-blur-md' : 'bg-transparent'"
                class="fixed inset-x-0 top-0 z-40 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-6 sm:px-8 h-20 flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="shrink-0">
                    <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-10 w-auto">
                </a>

                <nav class="hidden lg:flex items-center gap-8 text-sm font-medium"
                     :class="scrolled ? 'text-ink' : 'text-white'">
                    <a href="{{ route('home') }}" class="hover:text-brand-500 transition-colors">Início</a>
                    <a href="{{ route('estoque') }}" class="hover:text-brand-500 transition-colors">Estoque</a>
                    <a href="{{ route('cliente.dashboard') }}" class="hover:text-brand-500 transition-colors">Área do Cliente</a>
                </nav>

                <div class="hidden lg:flex items-center gap-3 shrink-0">
                    @if ($empresa?->whatsappUrl())
                        <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
                           class="rounded-full bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.03]">
                            WhatsApp
                        </a>
                    @endif
                    <a href="{{ route('login') }}" title="Administração"
                       class="w-9 h-9 flex items-center justify-center rounded-full border transition-colors"
                       :class="scrolled ? 'border-brand-100 text-muted hover:border-brand-500 hover:text-brand-700' : 'border-white/40 text-white hover:border-white'">
                        <x-heroicon-o-lock-closed class="w-4 h-4" />
                    </a>
                </div>
            </div>
        </header>

        <main class="{{ $heroTransparente ? '' : 'pt-20' }}">
            {{ $slot }}
        </main>
    </div>

    <footer id="contato-footer" class="relative bg-brand-900 pt-16 pb-10 text-brand-100">
        <div class="absolute inset-x-0 top-0 h-1.5" style="background-image: linear-gradient(135deg, #1268A3 0%, #4FA8E8 55%, #7FD3F0 100%)"></div>

        <div class="max-w-7xl mx-auto px-6 sm:px-8">
            <div class="grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="{{ $empresa->nome ?? config('app.name') }}" class="h-9 w-auto mb-4">
                    <p class="text-sm text-brand-100/80">{{ $empresa->endereco ?? '' }}</p>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-white uppercase">Contato</h3>
                    <ul class="flex flex-col gap-3 text-sm">
                        @if ($empresa?->telefone)
                            <li class="flex items-center gap-2">
                                <x-heroicon-o-phone class="size-4 shrink-0 text-brand-300" />
                                <a href="tel:{{ $empresa->telefone }}" class="hover:text-white">{{ $empresa->telefone }}</a>
                            </li>
                        @endif
                        @if ($empresa?->email_contato)
                            <li class="flex items-center gap-2">
                                <x-heroicon-o-envelope class="size-4 shrink-0 text-brand-300" />
                                <a href="mailto:{{ $empresa->email_contato }}" class="hover:text-white">{{ $empresa->email_contato }}</a>
                            </li>
                        @endif
                        @if ($empresa?->horario_funcionamento)
                            <li class="flex items-center gap-2">
                                <x-heroicon-o-clock class="size-4 shrink-0 text-brand-300" />
                                {{ $empresa->horario_funcionamento }}
                            </li>
                        @endif
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-white uppercase">Redes</h3>
                    @if ($empresa?->instagram_url)
                        <a href="{{ $empresa->instagram_url }}" target="_blank" rel="noopener" class="block text-sm hover:text-white">Instagram</a>
                    @endif
                    @if ($empresa?->facebook_url)
                        <a href="{{ $empresa->facebook_url }}" target="_blank" rel="noopener" class="mt-3 block text-sm hover:text-white">Facebook</a>
                    @endif
                </div>

                <div>
                    <h3 class="mb-4 text-sm font-semibold tracking-wide text-white uppercase">Área do Cliente</h3>
                    <a href="{{ route('cliente.dashboard') }}" class="block text-sm hover:text-white">Entrar / Cadastrar</a>
                </div>
            </div>

            <div class="mt-14 border-t border-white/10 pt-6 text-center text-xs text-brand-100/60">
                &copy; {{ date('Y') }} {{ $empresa->nome ?? config('app.name') }}. Todos os direitos reservados.
            </div>
        </div>
    </footer>

    @if ($empresa?->whatsappUrl())
        <a href="{{ $empresa->whatsappUrl() }}" target="_blank" rel="noopener"
           class="fixed bottom-5 right-5 z-40 w-14 h-14 rounded-full bg-brand-500 text-brand-900 flex items-center justify-center shadow-brand hover:scale-105 transition-transform"
           title="Fale conosco via WhatsApp">
            <x-heroicon-o-chat-bubble-left-right class="w-6 h-6" />
        </a>
    @endif

    @livewireScripts
</body>
</html>
