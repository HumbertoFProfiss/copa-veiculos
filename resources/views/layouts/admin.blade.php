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
    <div x-data="{ colapsada: localStorage.getItem('sidebarColapsada') === 'true' }"
         x-init="$watch('colapsada', v => localStorage.setItem('sidebarColapsada', v))"
         class="flex min-h-screen">

        <!-- Sidebar -->
        <aside :class="colapsada ? 'w-[72px]' : 'w-64'"
               class="shrink-0 border-r border-border bg-bg transition-all duration-200 flex flex-col">
            <div class="h-16 flex items-center px-4 border-b border-border">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="h-8 w-auto shrink-0">
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-1">
                <x-admin.sidebar-link route="dashboard" icon="squares-2x2" label="Dashboard" />

                @can('veiculos.ver')
                    <x-admin.sidebar-link route="admin.veiculos.index" icon="truck" label="Estoque" />
                @endcan
                @can('clientes.ver')
                    <x-admin.sidebar-link route="admin.clientes.index" icon="users" label="Clientes" />
                @endcan
                @can('fornecedores.ver')
                    <x-admin.sidebar-link route="admin.fornecedores.index" icon="building-storefront" label="Fornecedores" />
                @endcan
                @can('leads.ver')
                    <x-admin.sidebar-link route="admin.crm.pipeline" icon="chat-bubble-left-right" label="CRM" />
                    <x-admin.sidebar-link route="admin.leads.inbox" icon="inbox" label="Caixa de Leads" />
                @endcan
                @can('vendas.ver')
                    <x-admin.sidebar-link route="admin.vendas.index" icon="currency-dollar" label="Vendas" />
                @endcan
                @can('contratos.ver')
                    <x-admin.sidebar-link route="admin.contratos.index" icon="document-text" label="Contratos" />
                @endcan
                @can('financeiro.ver')
                    <x-admin.sidebar-link route="admin.financeiro.index" icon="banknotes" label="Financeiro" />
                @endcan
                @can('anuncios.ver')
                    <x-admin.sidebar-link route="admin.anuncios.index" icon="megaphone" label="Anúncios" />
                @endcan
                @can('importacoes.ver')
                    <x-admin.sidebar-link route="admin.importacoes.index" icon="arrow-up-tray" label="Importar Estoque" />
                @endcan
                @can('relatorios.ver')
                    <x-admin.sidebar-link route="admin.relatorios.index" icon="chart-bar" label="Relatórios" />
                @endcan

                <div class="pt-3 mt-3 border-t border-border space-y-1">
                    @can('usuarios.ver')
                        <x-admin.sidebar-link route="admin.usuarios.index" icon="user-group" label="Usuários" />
                    @endcan
                    @can('configuracoes.ver')
                        <x-admin.sidebar-link route="admin.configuracoes.index" icon="cog-6-tooth" label="Configurações" />
                    @endcan
                </div>
            </nav>

            <div class="p-3 border-t border-border">
                <button @click="colapsada = !colapsada" type="button"
                        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-control text-text-secondary hover:bg-surface text-sm">
                    <x-heroicon-o-chevron-double-left x-show="!colapsada" class="w-4 h-4" />
                    <x-heroicon-o-chevron-double-right x-show="colapsada" class="w-4 h-4" />
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 shrink-0 border-b border-border bg-bg flex items-center justify-between px-6 gap-4">
                <div class="text-sm text-text-secondary">
                    @if (app()->bound('tenant') && app('tenant'))
                        {{ app('tenant')->nome }}
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <button type="button" class="relative text-text-secondary hover:text-text-primary" aria-label="Notificações">
                        <x-heroicon-o-bell class="w-5 h-5" />
                    </button>

                    <div class="relative" x-data="{ aberto: false }">
                        <button @click="aberto = !aberto" type="button" class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-primary-soft text-primary flex items-center justify-center text-sm font-semibold">
                                {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 1)) }}
                            </span>
                        </button>
                        <div x-show="aberto" @click.outside="aberto = false" x-transition
                             class="absolute right-0 mt-2 w-48 bg-bg border border-border rounded-card shadow-sm py-1 z-10"
                             style="display: none;">
                            <div class="px-4 py-2 text-sm text-text-primary border-b border-border">
                                {{ auth()->user()->name }}
                            </div>
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-text-secondary hover:bg-surface">Meu perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-error hover:bg-surface">Sair</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6 bg-surface">
                @if (session('sucesso'))
                    <div class="mb-4 px-4 py-3 rounded-card bg-success/10 text-success text-sm">
                        {{ session('sucesso') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
