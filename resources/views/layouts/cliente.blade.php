<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function () {
            var salvo = localStorage.getItem('tema');
            var escuro = salvo ? salvo === 'escuro' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', escuro);
        })();
    </script>

    <title>{{ $title ?? 'Área do Cliente' }} - {{ app('tenant')?->nome ?? config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700|fraunces:500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-poppins antialiased bg-brand-100/40 dark:bg-duskbg text-ink dark:text-brand-100 min-h-screen flex flex-col"
      x-data="{
          escuro: document.documentElement.classList.contains('dark'),
          alternarTema() {
              this.escuro = !this.escuro;
              document.documentElement.classList.toggle('dark', this.escuro);
              localStorage.setItem('tema', this.escuro ? 'escuro' : 'claro');
          },
      }">

    <header class="bg-white dark:bg-duskcard border-b border-brand-100 dark:border-duskborder">
        <div class="max-w-5xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img src="{{ asset('logo.png') }}" alt="{{ app('tenant')?->nome }}" class="h-12 w-auto dark:hidden">
                <img src="{{ asset('logo-dark.png') }}" alt="{{ app('tenant')?->nome }}" class="h-12 w-auto hidden dark:block">
            </a>
            <div class="flex items-center gap-4 text-sm">
                <button type="button" @click="alternarTema()" title="Alternar modo escuro"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-brand-100 dark:border-duskborder text-muted dark:text-duskmuted hover:border-brand-500 hover:text-brand-700 dark:hover:text-brand-300 transition-colors">
                    <x-heroicon-o-moon class="w-4 h-4" x-show="!escuro" x-cloak />
                    <x-heroicon-o-sun class="w-4 h-4" x-show="escuro" x-cloak />
                </button>
                @auth('cliente')
                    <span class="text-muted dark:text-duskmuted hidden sm:inline">Olá, {{ explode(' ', Auth::guard('cliente')->user()->nome)[0] }}</span>
                    <form method="POST" action="{{ route('cliente.logout') }}">
                        @csrf
                        <button type="submit" class="text-muted dark:text-duskmuted hover:text-brand-700">Sair</button>
                    </form>
                @else
                    <a href="{{ route('home') }}" class="text-sm text-muted dark:text-duskmuted hover:text-brand-700">Voltar ao site</a>
                @endauth
            </div>
        </div>
    </header>

    @auth('cliente')
        <div class="bg-white dark:bg-duskcard border-b border-brand-100 dark:border-duskborder">
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
                           {{ $rotaAtual === $rota ? 'border-brand-700 text-brand-700 dark:text-brand-300' : 'border-transparent text-muted dark:text-duskmuted hover:text-brand-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
        </div>
    @endauth

    <main class="flex-1 max-w-5xl mx-auto px-6 py-12 w-full">
        {{ $slot }}
    </main>

    <footer class="border-t border-brand-100 dark:border-duskborder mt-auto bg-white dark:bg-duskcard">
        <div class="max-w-5xl mx-auto px-6 py-6 text-center text-xs text-muted dark:text-duskmuted">
            &copy; {{ date('Y') }} {{ app('tenant')?->nome ?? config('app.name') }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
