<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin' }} - {{ config('app.name') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-bg text-text-primary">
    <div x-data="{
                colapsada: localStorage.getItem('sidebarColapsada') === 'true',
                buscaGlobalAberta: false,
                abrirBuscaGlobal() {
                    this.buscaGlobalAberta = true;
                    $nextTick(() => {
                        const input = $refs.buscaGlobalInput;
                        if (! input) return;
                        input.value = '';
                        input.dispatchEvent(new Event('input'));
                        input.focus();
                    });
                },
            }"
         x-init="$watch('colapsada', v => localStorage.setItem('sidebarColapsada', v))"
         @keydown.window="if ((event.ctrlKey || event.metaKey) && event.key === 'k') { event.preventDefault(); abrirBuscaGlobal(); }"
         @keydown.window.escape="buscaGlobalAberta = false"
         class="flex min-h-screen">

        <!-- Sidebar -->
        <aside :class="colapsada ? 'w-[72px]' : 'w-64'"
               class="shrink-0 bg-bg shadow-soft-md transition-all duration-200 flex flex-col relative z-10">
            <div class="h-16 flex items-center px-4 border-b border-border">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="h-10 w-auto shrink-0">
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-4">
                <div class="space-y-1">
                    <x-admin.sidebar-link route="dashboard" icon="squares-2x2" label="Dashboard" />
                </div>

                @if (Auth::user()->can('veiculos.ver') || Auth::user()->can('clientes.ver') || Auth::user()->can('fornecedores.ver'))
                    <div class="space-y-1">
                        <div x-show="!colapsada" x-transition.opacity class="px-3 text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wider mb-1">Operação</div>
                        @can('veiculos.ver')
                            <x-admin.sidebar-link route="admin.veiculos.index" icon="truck" label="Estoque" />
                        @endcan
                        @can('clientes.ver')
                            <x-admin.sidebar-link route="admin.clientes.index" icon="users" label="Clientes" modulo="clientes" />
                        @endcan
                        @can('fornecedores.ver')
                            <x-admin.sidebar-link route="admin.fornecedores.index" icon="building-storefront" label="Fornecedores" modulo="fornecedores" />
                        @endcan
                        @can('importacoes.ver')
                            <x-admin.sidebar-link route="admin.importacoes.index" icon="arrow-up-tray" label="Importar Estoque" modulo="importacoes" />
                        @endcan
                    </div>
                @endif

                @if (Auth::user()->can('leads.ver') || Auth::user()->can('vendas.ver') || Auth::user()->can('contratos.ver'))
                    <div class="space-y-1">
                        <div x-show="!colapsada" x-transition.opacity class="px-3 text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wider mb-1">Relacionamento</div>
                        @can('leads.ver')
                            <x-admin.sidebar-link route="admin.crm.pipeline" icon="chat-bubble-left-right" label="CRM" />
                            <x-admin.sidebar-link route="admin.leads.inbox" icon="inbox" label="Caixa de Leads" />
                            <x-admin.sidebar-link route="admin.chamadas.index" icon="phone" label="Chamadas e Propostas" />
                        @endcan
                        @can('vendas.ver')
                            <x-admin.sidebar-link route="admin.vendas.index" icon="currency-dollar" label="Vendas" modulo="vendas" />
                        @endcan
                        @can('contratos.ver')
                            <x-admin.sidebar-link route="admin.contratos.index" icon="document-text" label="Contratos" modulo="contratos" />
                        @endcan
                    </div>
                @endif

                @if (Auth::user()->can('financeiro.ver') || Auth::user()->can('anuncios.ver') || Auth::user()->can('relatorios.ver'))
                    <div class="space-y-1">
                        <div x-show="!colapsada" x-transition.opacity class="px-3 text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wider mb-1">Gestão</div>
                        @can('financeiro.ver')
                            <x-admin.sidebar-link route="admin.financeiro.index" icon="banknotes" label="Financeiro" modulo="financeiro" />
                        @endcan
                        @can('anuncios.ver')
                            <x-admin.sidebar-link route="admin.anuncios.index" icon="megaphone" label="Anúncios" modulo="anuncios" />
                        @endcan
                        @can('relatorios.ver')
                            <x-admin.sidebar-link route="admin.relatorios.index" icon="chart-bar" label="Relatórios" modulo="relatorios" />
                        @endcan
                    </div>
                @endif

                @if (Auth::user()->can('usuarios.ver') || Auth::user()->can('configuracoes.ver') || Auth::user()->can('integracoes.ver') || Auth::user()->can('filiais.ver'))
                    <div class="space-y-1 pt-3 border-t border-border">
                        <div x-show="!colapsada" x-transition.opacity class="px-3 text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wider mb-1">Sistema</div>
                        @can('usuarios.ver')
                            <x-admin.sidebar-link route="admin.usuarios.index" icon="user-group" label="Usuários" modulo="usuarios" />
                        @endcan
                        @can('configuracoes.ver')
                            <x-admin.sidebar-link route="admin.configuracoes.index" icon="cog-6-tooth" label="Configurações" />
                        @endcan
                        @can('integracoes.ver')
                            <x-admin.sidebar-link route="admin.integracoes.index" icon="code-bracket" label="Integrações" modulo="integracoes" />
                        @endcan
                        @can('filiais.ver')
                            <x-admin.sidebar-link route="admin.filiais.index" icon="building-office-2" label="Filiais" modulo="filiais" />
                        @endcan
                    </div>
                @endif
            </nav>

            <div class="p-3 border-t border-border">
                <button @click="colapsada = !colapsada" type="button"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-control text-text-secondary hover:bg-surface hover:text-primary transition-colors text-sm">
                    <x-heroicon-o-chevron-double-left x-show="!colapsada" class="w-4 h-4" />
                    <x-heroicon-o-chevron-double-right x-show="colapsada" class="w-4 h-4" />
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 shrink-0 bg-bg/80 backdrop-blur border-b border-border flex items-center justify-between px-6 gap-4 sticky top-0 z-20">
                <div class="flex items-center gap-2.5">
                    @if (app()->bound('tenant') && app('tenant'))
                        <span class="w-2 h-2 rounded-full bg-success shrink-0"></span>
                        <span class="text-sm font-medium text-text-primary">{{ app('tenant')->nome }}</span>
                        @if (app('tenant')->plano ?? false)
                            <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-control text-[11px] font-semibold bg-primary-soft text-primary uppercase tracking-wide">
                                {{ str_replace('_', ' ', app('tenant')->plano) }}
                            </span>
                        @endif
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="abrirBuscaGlobal()"
                            class="hidden sm:flex items-center gap-2 px-3 h-9 rounded-control border border-border text-text-secondary hover:bg-surface hover:text-text-primary transition-colors text-sm">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                        <span>Buscar</span>
                        <kbd class="text-[11px] px-1.5 py-0.5 rounded border border-border">Ctrl K</kbd>
                    </button>
                    <button type="button" @click="abrirBuscaGlobal()" aria-label="Buscar"
                            class="sm:hidden w-9 h-9 flex items-center justify-center rounded-control text-text-secondary hover:bg-surface hover:text-text-primary transition-colors">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </button>

                    <button type="button" class="relative w-9 h-9 flex items-center justify-center rounded-control text-text-secondary hover:bg-surface hover:text-text-primary transition-colors" aria-label="Notificações">
                        <x-heroicon-o-bell class="w-5 h-5" />
                    </button>

                    <div class="w-px h-6 bg-border"></div>

                    <div class="relative" x-data="{ aberto: false }">
                        <button @click="aberto = !aberto" type="button" class="flex items-center gap-2 px-1.5 py-1 rounded-control hover:bg-surface transition-colors">
                            <span class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-semibold shadow-soft">
                                {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                            </span>
                            <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-text-secondary hidden sm:block" />
                        </button>
                        <div x-show="aberto" @click.outside="aberto = false" x-transition
                             class="absolute right-0 mt-2 w-52 bg-bg border border-border rounded-card shadow-soft-lg py-1 z-30 overflow-hidden"
                             style="display: none;">
                            <div class="px-4 py-3 border-b border-border">
                                <div class="text-sm font-medium text-text-primary">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-text-secondary">{{ auth()->user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-text-secondary hover:bg-surface hover:text-text-primary">
                                <x-heroicon-o-user-circle class="w-4 h-4" /> Meu perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 text-left px-4 py-2 text-sm text-error hover:bg-error/5">
                                    <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4" /> Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 bg-surface">
                @if (session('sucesso'))
                    <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
                        <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
                        {{ session('sucesso') }}
                    </div>
                @endif

                @if (session('erro'))
                    <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-error/10 text-error text-sm border border-error/20">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0" />
                        {{ session('erro') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
        <div x-show="buscaGlobalAberta" x-cloak
             class="fixed inset-0 z-50 flex items-start justify-center pt-24 px-4 bg-black/40"
             @click.self="buscaGlobalAberta = false">
            <div x-show="buscaGlobalAberta" x-transition.opacity.duration.150ms
                 class="w-full max-w-xl bg-bg border border-border rounded-card shadow-soft-lg overflow-hidden">
                @livewire('shared.global-search')
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
