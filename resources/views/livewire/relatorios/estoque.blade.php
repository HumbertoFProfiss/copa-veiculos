<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Relatório de Estoque</h1>
        <button wire:click="exportar" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Exportar XLSX
        </button>
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
