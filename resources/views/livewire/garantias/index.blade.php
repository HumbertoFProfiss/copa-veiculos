<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Garantias</h1>
        <div class="text-sm text-text-secondary">
            Custo total (filtro atual): <span class="font-semibold text-text-primary tabular-nums">R$ {{ number_format($totalCustos, 2, ',', '.') }}</span>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[240px]">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" wire:model.live.debounce.300ms="busca"
                   placeholder="Buscar por carro, cliente ou descrição do problema..."
                   class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>
        <select wire:model.live="filtroStatus" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os status</option>
            @foreach ($statusLabels as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Problema</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Custo</th>
            <x-admin.th-sort coluna="created_at" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Aberto em</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($chamados as $chamado)
            <tr wire:key="chamado-{{ $chamado->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $chamado->veiculo?->marca }} {{ $chamado->veiculo?->modelo }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $chamado->cliente?->nome }}</td>
                <td class="px-4 py-3 text-text-secondary max-w-xs truncate">{{ $chamado->descricao_problema }}</td>
                <td class="px-4 py-3 tabular-nums text-text-primary">R$ {{ number_format((float) $chamado->custo_peca + (float) $chamado->custo_servico, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $chamado->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge
                        :variant="match($chamado->status) { 'aprovado', 'concluido' => 'success', 'em_analise' => 'warning', 'recusado' => 'error', default => 'neutral' }"
                        :label="$chamado->statusLabel()" />
                </td>
                <td class="px-4 py-3 text-right">
                    @if ($chamado->venda)
                        <a href="{{ route('admin.vendas.show', $chamado->venda) }}" class="text-xs text-primary hover:underline">Ver venda</a>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum chamado de garantia registrado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $chamados->links() }}</div>
</div>
