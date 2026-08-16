<div class="max-w-2xl">
    <x-financeiro.tabs ativo="dre" />

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">DRE - Demonstrativo de Resultado</h1>
        <input type="month" wire:model.live="mes" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
    </div>

    <div class="bg-bg border border-border rounded-card divide-y divide-border">
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">Vendas no período</span>
            <span class="text-sm font-medium text-text-primary tabular-nums">{{ $totalVendas }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">Receita bruta</span>
            <span class="text-sm font-medium text-success tabular-nums">R$ {{ number_format($receitaBruta, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">(-) Custo dos veículos vendidos</span>
            <span class="text-sm font-medium text-error tabular-nums">R$ {{ number_format($custoVeiculos, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">(-) Custos agregados (preparação)</span>
            <span class="text-sm font-medium text-error tabular-nums">R$ {{ number_format($custosAgregados, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">(-) Custos de garantia</span>
            <span class="text-sm font-medium text-error tabular-nums">R$ {{ number_format($custosGarantia, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">(-) Comissões</span>
            <span class="text-sm font-medium text-error tabular-nums">R$ {{ number_format($comissoes, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-3">
            <span class="text-sm text-text-secondary">(-) Despesas operacionais pagas</span>
            <span class="text-sm font-medium text-error tabular-nums">R$ {{ number_format($despesasOperacionais, 2, ',', '.') }}</span>
        </div>
        <div class="flex items-center justify-between px-5 py-4 bg-surface rounded-b-card">
            <span class="text-sm font-semibold text-text-primary">Lucro líquido</span>
            <span class="text-base font-semibold tabular-nums {{ $lucroLiquido >= 0 ? 'text-success' : 'text-error' }}">
                R$ {{ number_format($lucroLiquido, 2, ',', '.') }}
            </span>
        </div>
    </div>
</div>
