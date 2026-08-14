<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Vendas</h1>
        @can('vendas.criar')
            <a href="{{ route('admin.vendas.nova') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Nova venda
            </a>
        @endcan
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Vendedor</th>
            <x-admin.th-sort coluna="preco_venda" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Valor</x-admin.th-sort>
            <x-admin.th-sort coluna="data_venda" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Data</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($vendas as $venda)
            <tr wire:key="venda-{{ $venda->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">
                    <a href="{{ route('admin.vendas.show', $venda) }}" class="hover:text-primary hover:underline">{{ $venda->veiculo?->marca ?? 'Veículo removido' }} {{ $venda->veiculo?->modelo }}</a>
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $venda->cliente->nome }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $venda->vendedor->name }}</td>
                <td class="px-4 py-3 tabular-nums text-text-primary">R$ {{ number_format($venda->preco_venda - $venda->desconto, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $venda->data_venda->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="match($venda->status) { 'confirmada' => 'success', 'cancelada' => 'error', default => 'warning' }" :label="ucfirst($venda->status)" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.vendas.show', $venda) }}" class="text-xs text-text-secondary hover:text-primary hover:underline">Ver detalhes</a>
                        @can('contratos.criar')
                            <a href="{{ route('admin.contratos.gerar', $venda) }}" class="text-xs text-primary hover:underline">Gerar contrato</a>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma venda registrada.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $vendas->links() }}</div>
</div>
