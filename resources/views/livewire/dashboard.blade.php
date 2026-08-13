<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Olá, {{ explode(' ', auth()->user()->name)[0] }}</h1>
            <p class="text-sm text-text-secondary mt-0.5">Resumo da operação em {{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>
    </div>

    @if ($leadsEmAtraso > 0)
        <a href="{{ route('admin.leads.inbox') }}"
           class="flex items-center gap-3 mb-4 px-4 py-3 rounded-card bg-error/10 border border-error/20 text-error text-sm hover:bg-error/15 transition-colors">
            <x-heroicon-o-exclamation-circle class="w-5 h-5 shrink-0" />
            <span>
                <strong>{{ $leadsEmAtraso }}</strong>
                {{ $leadsEmAtraso === 1 ? 'lead está' : 'leads estão' }}
                sem primeiro atendimento há mais de {{ \App\Livewire\Dashboard::SLA_MINUTOS }} minutos.
            </span>
        </a>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                    <x-heroicon-o-truck class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Veículos disponíveis</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">{{ $totalDisponiveis }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-success/10 text-success flex items-center justify-center">
                    <x-heroicon-o-banknotes class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Valor em estoque</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($valorEmEstoque, 0, ',', '.') }}</div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5 shadow-soft hover:shadow-soft-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                    <x-heroicon-o-currency-dollar class="w-5 h-5" />
                </div>
            </div>
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Vendas do mês</div>
            <div class="text-3xl font-semibold text-text-primary tabular-nums">{{ $vendasDoMes }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control bg-surface text-text-secondary flex items-center justify-center">
                <x-heroicon-o-clock class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Dias médios em pátio</div>
                <div class="text-lg font-semibold text-text-primary tabular-nums">{{ $diasMedioPatio }}</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control flex items-center justify-center {{ $margemMedia >= 0 ? 'bg-success/10 text-success' : 'bg-error/10 text-error' }}">
                <x-heroicon-o-chart-bar class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Margem média</div>
                <div class="text-lg font-semibold tabular-nums {{ $margemMedia >= 0 ? 'text-success' : 'text-error' }}">{{ $margemMedia }}%</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control bg-primary-soft text-primary flex items-center justify-center">
                <x-heroicon-o-chat-bubble-left-right class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Leads abertos</div>
                <div class="text-lg font-semibold text-text-primary tabular-nums">{{ $leadsAbertos }}</div>
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-4 shadow-soft flex items-center gap-3">
            <div class="w-9 h-9 shrink-0 rounded-control flex items-center justify-center {{ $contasAVencer > 0 ? 'bg-warning/10 text-warning' : 'bg-surface text-text-secondary' }}">
                <x-heroicon-o-exclamation-triangle class="w-4.5 h-4.5" />
            </div>
            <div class="min-w-0">
                <div class="text-xs text-text-secondary truncate">Contas a vencer (7 dias)</div>
                <div class="text-lg font-semibold tabular-nums {{ $contasAVencer > 0 ? 'text-warning' : 'text-text-primary' }}">R$ {{ number_format($contasAVencer, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
