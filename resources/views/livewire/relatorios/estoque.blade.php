<div>
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-xl font-semibold text-text-primary">Relatório de Estoque</h1>
        <button wire:click="exportar" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Exportar XLSX
        </button>
    </div>

    <div class="flex items-center gap-4 mb-6 text-sm border-b border-border">
        <span class="px-1 pb-2 -mb-px border-b-2 border-primary text-primary font-medium">Estoque (ABC)</span>
        <a href="{{ route('admin.relatorios.construtor') }}" class="px-1 pb-2 text-text-secondary hover:text-text-primary">Construtor de relatórios</a>
    </div>

    <div class="bg-primary-soft/50 border border-primary/20 rounded-card p-4 mb-6">
        <p class="text-xs font-semibold text-primary uppercase tracking-wide mb-2">O que significa a Classe ABC</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
            <div class="flex items-start gap-2">
                <x-admin.status-badge variant="success" label="A" class="mt-0.5" />
                <span class="text-text-secondary">Os veículos mais valiosos — juntos somam até <strong class="text-text-primary">{{ $abcLimiteA }}%</strong> do valor total em estoque. Merecem mais atenção (fotos, anúncio, negociação).</span>
            </div>
            <div class="flex items-start gap-2">
                <x-admin.status-badge variant="warning" label="B" class="mt-0.5" />
                <span class="text-text-secondary">Importância intermediária — de {{ $abcLimiteA }}% até <strong class="text-text-primary">{{ $abcLimiteB }}%</strong> do valor acumulado.</span>
            </div>
            <div class="flex items-start gap-2">
                <x-admin.status-badge variant="neutral" label="C" class="mt-0.5" />
                <span class="text-text-secondary">Os {{ 100 - $abcLimiteB }}% finais — menor impacto individual no valor total do estoque.</span>
            </div>
        </div>
        <p class="text-xs text-text-secondary mt-2">Os percentuais de corte são configuráveis em <a href="{{ route('admin.configuracoes.index') }}" class="text-primary hover:underline">Configurações</a>.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-bg border border-border rounded-card p-4">
            <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Valor imobilizado</div>
            <div class="text-xl font-semibold text-text-primary tabular-nums">R$ {{ number_format($valorImobilizado, 0, ',', '.') }}</div>
        </div>
        @foreach (['A', 'B', 'C'] as $classe)
            <div class="bg-bg border border-border rounded-card p-4">
                <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Classe {{ $classe }}</div>
                <div class="text-xl font-semibold text-text-primary tabular-nums">{{ $contagemPorClasse->get($classe, 0) }} veículo(s)</div>
            </div>
        @endforeach
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Dias em pátio</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Valor</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">% acumulado</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Classe ABC</th>
        </x-slot:head>
        @forelse ($classificados as $item)
            <tr class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $item['veiculo']->marca }} {{ $item['veiculo']->modelo }}</td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $item['veiculo']->diasEmPatio() }}</td>
                <td class="px-4 py-3 text-text-primary tabular-nums">R$ {{ number_format($item['veiculo']->preco_venda, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $item['percentualAcumulado'] }}%</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge
                        :variant="$item['classe'] === 'A' ? 'success' : ($item['classe'] === 'B' ? 'warning' : 'neutral')"
                        :label="'Classe '.$item['classe']" />
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum veículo disponível.</td></tr>
        @endforelse
    </x-admin.data-table>
</div>
