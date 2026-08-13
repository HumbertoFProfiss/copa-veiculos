<div>
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-xl font-semibold text-text-primary">Construtor de Relatórios</h1>
        <button wire:click="exportar" type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
            Exportar XLSX
        </button>
    </div>

    <div class="flex items-center gap-4 mb-6 text-sm border-b border-border">
        <a href="{{ route('admin.relatorios.index') }}" class="px-1 pb-2 text-text-secondary hover:text-text-primary">Estoque (ABC)</a>
        <span class="px-1 pb-2 -mb-px border-b-2 border-primary text-primary font-medium">Construtor de relatórios</span>
    </div>

    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Fonte de dados</label>
                <select wire:model.live="fonte" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @foreach ($fontes as $chave => $def)
                        <option value="{{ $chave }}">{{ $def['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Período - de</label>
                <input type="date" wire:model.live="dataInicio" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Período - até</label>
                <input type="date" wire:model.live="dataFim" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            </div>
            @if (! empty($definicao['campos_agrupaveis']))
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Agrupar por</label>
                    <select wire:model.live="agruparPor" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        <option value="">Não agrupar (listar linhas)</option>
                        @foreach ($definicao['campos_agrupaveis'] as $campo)
                            <option value="{{ $campo }}">{{ $definicao['campos'][$campo] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        @unless ($agrupado)
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-2">Colunas</label>
                <div class="flex flex-wrap gap-3">
                    @foreach ($definicao['campos'] as $campo => $label)
                        <label class="inline-flex items-center gap-1.5 text-sm text-text-secondary">
                            <input type="checkbox" wire:model.live="camposSelecionados" value="{{ $campo }}"
                                   class="rounded-control border-border text-primary focus:ring-primary">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endunless
    </div>

    @if ($agrupado)
        <x-admin.data-table>
            <x-slot:head>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">{{ $definicao['campos'][$agruparPor] }}</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Quantidade</th>
                @if ($definicao['campo_valor'])
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Soma</th>
                @endif
            </x-slot:head>

            @forelse ($agrupado as $linha)
                <tr class="hover:bg-surface">
                    <td class="px-4 py-3 font-medium text-text-primary">{{ $linha->rotulo }}</td>
                    <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $linha->quantidade }}</td>
                    @if ($definicao['campo_valor'])
                        <td class="px-4 py-3 text-text-secondary tabular-nums">R$ {{ number_format($linha->soma ?? 0, 2, ',', '.') }}</td>
                    @endif
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum resultado no período.</td></tr>
            @endforelse
        </x-admin.data-table>
    @else
        <x-admin.data-table>
            <x-slot:head>
                @foreach ($camposSelecionados as $campo)
                    <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">{{ $definicao['campos'][$campo] ?? $campo }}</th>
                @endforeach
            </x-slot:head>

            @forelse ($linhas as $item)
                <tr class="hover:bg-surface">
                    @foreach ($camposSelecionados as $campo)
                        <td class="px-4 py-3 text-text-secondary">
                            @php $valor = $item->{$campo}; @endphp
                            @if (isset($definicao['labels_valor'][$campo]))
                                {{ $definicao['labels_valor'][$campo][$valor] ?? $valor }}
                            @elseif (is_bool($valor))
                                {{ $valor ? 'Sim' : 'Não' }}
                            @elseif ($valor instanceof \Illuminate\Support\Carbon)
                                {{ $valor->format('d/m/Y') }}
                            @else
                                {{ $valor ?? '—' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($camposSelecionados) }}" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum resultado no período.</td></tr>
            @endforelse
        </x-admin.data-table>

        <div class="mt-4">{{ $linhas->links() }}</div>
    @endif
</div>
