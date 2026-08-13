@php $empresa = app()->bound('tenant') ? app('tenant') : null; @endphp
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
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">

    {{-- Open Graph / redes sociais --}}
    <meta property="og:type" content="{{ $ogType ?? 'website' }}">
    <meta property="og:title" content="{{ $title ?? config('app.name') }}">
    <meta property="og:description" content="{{ $description ?? ($empresa->sobre_texto ?? config('app.name')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $empresa->nome ?? config('app.name') }}">
    <meta property="og:image" content="{{ $ogImage ?? asset('logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|fraunces:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if ($empresa?->analytics_ga4_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $empresa->analytics_ga4_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $empresa->analytics_ga4_id }}');
        </script>
    @endif
    @if ($empresa?->analytics_gtm_id)
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $empresa->analytics_gtm_id }}');</script>
    @endif
    @if ($empresa?->analytics_meta_pixel_id)
        <script>
            !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '{{ $empresa->analytics_meta_pixel_id }}');
            fbq('track', 'PageView');
        </script>
    @endif
</head>
<body class="font-poppins antialiased bg-white text-ink">
    @if ($empresa?->analytics_gtm_id)
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $empresa->analytics_gtm_id }}"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    @endif

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
