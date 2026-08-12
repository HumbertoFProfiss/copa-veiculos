<div>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Contas a Receber</h1>
        <p class="text-sm text-text-secondary mt-1">Total pendente: <span class="tabular-nums font-medium text-success">R$ {{ number_format($totalPendente, 2, ',', '.') }}</span></p>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Descrição</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <x-admin.th-sort coluna="valor" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Valor</x-admin.th-sort>
            <x-admin.th-sort coluna="vencimento" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Vencimento</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @forelse ($contas as $conta)
            <tr wire:key="cr-{{ $conta->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $conta->descricao }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $conta->cliente?->nome ?? '—' }}</td>
                <td class="px-4 py-3 tabular-nums text-text-primary">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $conta->vencimento->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$conta->status === 'recebido' ? 'success' : 'warning'" :label="ucfirst($conta->status)" />
                </td>
                <td class="px-4 py-3 text-right">
                    @can('financeiro.aprovar')
                        @if ($conta->status === 'pendente')
                            <button wire:click="marcarRecebido({{ $conta->id }})" type="button" class="text-xs text-primary hover:underline">Marcar recebido</button>
                        @endif
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma conta a receber.</td></tr>
        @endforelse
    </x-admin.data-table>
    <div class="mt-4">{{ $contas->links() }}</div>
</div>
