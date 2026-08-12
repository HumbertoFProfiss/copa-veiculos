<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Estoque</h1>
        @can('veiculos.criar')
            <a href="{{ route('admin.veiculos.novo') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Novo veículo
            </a>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <div class="relative flex-1 min-w-[240px]">
            <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" wire:model.live.debounce.300ms="busca"
                   placeholder="Buscar por marca, modelo, placa, chassi..."
                   class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>

        <select wire:model.live="filtroStatus" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os status</option>
            @foreach ($statusOptions as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </select>

        @if (count($selecionados))
            <span class="text-sm text-text-secondary">{{ count($selecionados) }} selecionado(s)</span>
            <button wire:click="limparSelecao" type="button" class="text-sm text-primary hover:underline">Limpar</button>
        @endif
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="w-10 px-4 py-3"><input type="checkbox" wire:model.live="selecionarTodos"></th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Foto</th>
            <x-admin.th-sort coluna="modelo" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Veículo</x-admin.th-sort>
            <x-admin.th-sort coluna="ano_modelo" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Ano</x-admin.th-sort>
            <x-admin.th-sort coluna="km" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">KM</x-admin.th-sort>
            <x-admin.th-sort coluna="preco_venda" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Preço</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Dias em pátio</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($veiculos as $veiculo)
            <tr wire:key="veiculo-{{ $veiculo->id }}" class="hover:bg-surface">
                <td class="px-4 py-3">
                    <input type="checkbox" value="{{ $veiculo->id }}" wire:model.live="selecionados">
                </td>
                <td class="px-4 py-3">
                    @if ($veiculo->fotos->isNotEmpty())
                        <img src="{{ $veiculo->fotos->first()->url() }}" class="w-14 h-10 object-cover rounded-control border border-border">
                    @else
                        <div class="w-14 h-10 rounded-control bg-surface border border-border flex items-center justify-center">
                            <x-heroicon-o-photo class="w-4 h-4 text-text-secondary" />
                        </div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('admin.veiculos.editar', $veiculo) }}" class="font-medium text-text-primary hover:text-primary">
                        {{ $veiculo->marca }} {{ $veiculo->modelo }}
                    </a>
                    @if ($veiculo->versao)
                        <div class="text-xs text-text-secondary">{{ $veiculo->versao }}</div>
                    @endif
                </td>
                <td class="px-4 py-3 tabular-nums text-text-secondary">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</td>
                <td class="px-4 py-3 tabular-nums text-text-secondary">{{ number_format($veiculo->km, 0, ',', '.') }}</td>
                <td class="px-4 py-3 tabular-nums text-text-primary font-medium">
                    @if ($veiculo->preco_venda)
                        R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
                    @else
                        <span class="text-text-secondary">—</span>
                    @endif
                </td>
                <td class="px-4 py-3 tabular-nums text-text-secondary">{{ $veiculo->diasEmPatio() }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$veiculo->statusVariant()" :label="$veiculo->statusLabel()" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.veiculos.editar', $veiculo) }}" class="text-text-secondary hover:text-primary">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                        </a>
                        @can('veiculos.excluir')
                            <button wire:click="excluir({{ $veiculo->id }})"
                                    wire:confirm="Tem certeza que quer excluir este veículo?"
                                    type="button" class="text-text-secondary hover:text-error">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-4 py-12 text-center text-text-secondary text-sm">
                    Nenhum veículo encontrado.
                </td>
            </tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">
        {{ $veiculos->links() }}
    </div>
</div>
