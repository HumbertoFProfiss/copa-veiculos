<div>
    <x-financeiro.tabs ativo="lucro-veiculos" />

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Lucro por Veículo</h1>
            <p class="text-sm text-text-secondary mt-1">Compra, custos, venda e margem de cada veículo, lado a lado.</p>
        </div>
        <select wire:model.live="filtroStatus" class="rounded-control border-border bg-white text-ink text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os status</option>
            @foreach ($statusOptions as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-bg border border-border rounded-card p-4">
            <p class="text-xs text-text-secondary mb-1">Total de compra</p>
            <p class="text-lg font-semibold text-text-primary tabular-nums">R$ {{ number_format($totalCompra, 2, ',', '.') }}</p>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <p class="text-xs text-text-secondary mb-1">Total de custos</p>
            <p class="text-lg font-semibold text-text-primary tabular-nums">R$ {{ number_format($totalCustos, 2, ',', '.') }}</p>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <p class="text-xs text-text-secondary mb-1">Total de venda</p>
            <p class="text-lg font-semibold text-text-primary tabular-nums">R$ {{ number_format($totalVenda, 2, ',', '.') }}</p>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <p class="text-xs text-text-secondary mb-1">Margem total</p>
            <p class="text-lg font-semibold {{ $totalMargem >= 0 ? 'text-success' : 'text-error' }} tabular-nums">R$ {{ number_format($totalMargem, 2, ',', '.') }}</p>
        </div>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wide">Compra</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wide">Custos</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wide">Venda</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-text-secondary uppercase tracking-wide">Margem</th>
        </x-slot:head>

        @forelse ($linhas as $linha)
            @php($v = $linha['veiculo'])
            <tr wire:key="lucro-{{ $v->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $v->marca }} {{ $v->modelo }} <span class="text-text-secondary font-normal">{{ $v->ano_modelo }}</span></td>
                <td class="px-4 py-3"><x-admin.status-badge :variant="$v->statusVariant()" :label="$v->statusLabel()" /></td>
                <td class="px-4 py-3 text-right tabular-nums text-text-primary">{{ $v->preco_compra ? 'R$ '.number_format($v->preco_compra, 2, ',', '.') : '—' }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-text-primary">R$ {{ number_format($linha['custos'], 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-right tabular-nums text-text-primary">{{ $v->preco_venda ? 'R$ '.number_format($v->preco_venda, 2, ',', '.') : '—' }}</td>
                <td class="px-4 py-3 text-right tabular-nums font-medium {{ $linha['margem'] === null ? 'text-text-secondary' : ($linha['margem'] >= 0 ? 'text-success' : 'text-error') }}">
                    {{ $linha['margem'] === null ? '—' : 'R$ '.number_format($linha['margem'], 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum veículo encontrado.</td></tr>
        @endforelse
    </x-admin.data-table>
</div>
