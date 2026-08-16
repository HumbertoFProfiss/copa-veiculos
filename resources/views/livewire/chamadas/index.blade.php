<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Chamadas e Propostas</h1>
            <p class="text-sm text-text-secondary mt-1">Registro de ligações, WhatsApp e visitas — inclusive clientes procurando um veículo que não temos em estoque.</p>
        </div>
        @can('leads.editar')
            <button wire:click="novo" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" /> Nova chamada
            </button>
        @endcan
    </div>

    <div class="flex flex-wrap items-center gap-3 mb-4">
        <select wire:model.live="filtroIntencao" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todas as intenções</option>
            @foreach (\App\Models\ChamadaProposta::INTENCAO_LABELS as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroResultado" class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <option value="">Todos os resultados</option>
            @foreach (\App\Models\ChamadaProposta::RESULTADO_LABELS as $valor => $label)
                <option value="{{ $valor }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cliente</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Tipo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Intenção</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Resultado</th>
            <x-admin.th-sort coluna="created_at" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Data</x-admin.th-sort>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($chamadas as $chamada)
            <tr wire:key="chamada-{{ $chamada->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $chamada->cliente?->nome ?? '—' }}</td>
                <td class="px-4 py-3 text-text-secondary">
                    @if ($chamada->veiculo)
                        {{ $chamada->veiculo->marca }} {{ $chamada->veiculo->modelo }}
                    @elseif ($chamada->veiculo_procurado)
                        <span class="italic">Procurando: {{ $chamada->veiculo_procurado }}</span>
                    @else
                        —
                    @endif
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $chamada->tipoLabel() }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $chamada->intencaoLabel() }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$chamada->resultadoVariant()" :label="$chamada->resultadoLabel()" />
                </td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $chamada->created_at->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('leads.editar')
                            <button wire:click="editar({{ $chamada->id }})" type="button" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                            <button wire:click="excluir({{ $chamada->id }})" wire:confirm="Excluir esta chamada?" type="button" class="text-text-secondary hover:text-error">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma chamada registrada.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $chamadas->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">{{ $editandoId ? 'Editar chamada' : 'Nova chamada' }}</h2>
                    <button type="button" wire:click="$set('mostrarForm', false)" class="text-text-secondary hover:text-text-primary" aria-label="Fechar">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit="salvar" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Cliente</label>
                            <select wire:model="cliente_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                <option value="">—</option>
                                @foreach ($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Tipo *</label>
                            <select wire:model="tipo" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                @foreach (\App\Models\ChamadaProposta::TIPO_LABELS as $valor => $label)
                                    <option value="{{ $valor }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Intenção *</label>
                        <select wire:model.live="intencao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @foreach (\App\Models\ChamadaProposta::INTENCAO_LABELS as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($intencao === 'comprar')
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Veículo já em estoque (se houver)</label>
                            <select wire:model="veiculo_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                <option value="">—</option>
                                @foreach ($veiculos as $veiculo)
                                    <option value="{{ $veiculo->id }}">{{ $veiculo->marca }} {{ $veiculo->modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Ou descreva o que o cliente procura</label>
                            <input type="text" wire:model.live.debounce.400ms="veiculo_procurado" placeholder="Ex: Corolla 2020 automático até R$ 90 mil"
                                   class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                        @if ($this->veiculosCompativeis->isNotEmpty())
                            <div class="rounded-control bg-primary-soft/50 border border-primary/20 p-3">
                                <p class="text-xs font-medium text-primary mb-2">Temos algo parecido no estoque atual:</p>
                                <ul class="space-y-1">
                                    @foreach ($this->veiculosCompativeis as $sugestao)
                                        <li class="text-sm text-text-primary">{{ $sugestao->marca }} {{ $sugestao->modelo }} {{ $sugestao->ano_modelo }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @else
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Veículo relacionado (se houver)</label>
                            <select wire:model="veiculo_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                <option value="">—</option>
                                @foreach ($veiculos as $veiculo)
                                    <option value="{{ $veiculo->id }}">{{ $veiculo->marca }} {{ $veiculo->modelo }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Resultado *</label>
                        <select wire:model="resultado" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @foreach (\App\Models\ChamadaProposta::RESULTADO_LABELS as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Observações</label>
                        <textarea wire:model="observacoes" rows="3" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary"></textarea>
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
