<div>
    <h1 class="text-xl font-semibold text-text-primary mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Veículos disponíveis</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">{{ $totalDisponiveis }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Valor em estoque</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($valorEmEstoque, 0, ',', '.') }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Dias médios em pátio</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">{{ $diasMedioPatio }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Vendas do mês</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">{{ $vendasDoMes }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Margem média</div>
            <div class="text-2xl font-semibold tabular-nums {{ $margemMedia >= 0 ? 'text-success' : 'text-error' }}">{{ $margemMedia }}%</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Leads abertos</div>
            <div class="text-2xl font-semibold text-text-primary tabular-nums">{{ $leadsAbertos }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-5">
            <div class="text-xs font-medium text-text-secondary uppercase tracking-wide mb-1">Contas a vencer (7 dias)</div>
            <div class="text-2xl font-semibold text-warning tabular-nums">R$ {{ number_format($contasAVencer, 0, ',', '.') }}</div>
        </div>
    </div>
</div>
