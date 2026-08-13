<div>
    <x-financeiro.tabs ativo="index" />

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Contas a Pagar</h1>
            <p class="text-sm text-text-secondary mt-1">Total pendente: <span class="tabular-nums font-medium text-error">R$ {{ number_format($totalPendente, 2, ',', '.') }}</span></p>
        </div>
        @can('financeiro.criar')
            <button wire:click="novo" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" /> Nova conta
            </button>
        @endcan
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Descrição</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Categoria</th>
            <x-admin.th-sort coluna="valor" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Valor</x-admin.th-sort>
            <x-admin.th-sort coluna="vencimento" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Vencimento</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>
        @forelse ($contas as $conta)
            <tr wire:key="cp-{{ $conta->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $conta->descricao }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $conta->categoria?->nomeCompleto() ?? '—' }}</td>
                <td class="px-4 py-3 tabular-nums text-text-primary">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $conta->vencimento->format('d/m/Y') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$conta->status === 'pago' ? 'success' : 'warning'" :label="ucfirst($conta->status)" />
                </td>
                <td class="px-4 py-3 text-right">
                    @can('financeiro.aprovar')
                        @if ($conta->status === 'pendente')
                            <button wire:click="marcarPago({{ $conta->id }})" type="button" class="text-xs text-primary hover:underline">Marcar pago</button>
                        @endif
                    @endcan
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma conta lançada.</td></tr>
        @endforelse
    </x-admin.data-table>
    <div class="mt-4">{{ $contas->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20" wire:click.self="$set('mostrarForm', false)">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">Nova conta a pagar</h2>
                <form wire:submit="salvar" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Descrição *</label>
                        <input type="text" wire:model="descricao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('descricao') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Valor *</label>
                            <input type="number" step="0.01" wire:model="valor" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                            @error('valor') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Vencimento *</label>
                            <input type="date" wire:model="vencimento" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Categoria</label>
                        <select wire:model="categoria_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="">—</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nomeCompleto() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Recorrência</label>
                        <select wire:model="recorrencia" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="nenhuma">Nenhuma</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensal">Mensal</option>
                            <option value="anual">Anual</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Salvar</button>
                        <button type="button" wire:click="$set('mostrarForm', false)" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
