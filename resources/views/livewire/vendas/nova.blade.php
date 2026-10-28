<div class="max-w-2xl">
    <h1 class="text-xl font-semibold text-text-primary mb-6">Nova venda</h1>

    <form wire:submit="salvar" class="bg-bg border border-border rounded-card p-6 space-y-4">
        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Veículo *</label>
            <select wire:model.live="veiculo_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <option value="">Selecione...</option>
                @foreach ($veiculos as $veiculo)
                    <option value="{{ $veiculo->id }}">{{ $veiculo->marca }} {{ $veiculo->modelo }} - {{ $veiculo->placa }}</option>
                @endforeach
            </select>
            @error('veiculo_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Cliente *</label>
            <select wire:model="cliente_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <option value="">Selecione...</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
            @error('cliente_id') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Vendedor *</label>
            <select wire:model="vendedor_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                @foreach ($vendedores as $vendedor)
                    <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Valor de venda *</label>
                <input type="number" step="0.01" wire:model.live.debounce.500ms="preco_venda" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                @error('preco_venda') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Desconto</label>
                <input type="number" step="0.01" wire:model="desconto" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                @if ($precoAnunciado)
                    <p class="text-xs text-text-secondary mt-1">Calculado automaticamente (anunciado R$ {{ number_format($precoAnunciado, 2, ',', '.') }} − valor de venda) — pode ajustar se precisar.</p>
                @endif
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Data da venda</label>
            <input type="date" wire:model="data_venda" class="w-full max-w-[220px] rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>

        {{-- Formas de pagamento: uma venda pode combinar varias (parte dinheiro, parte financiado, carro na troca...) --}}
        <div class="rounded-control border border-border bg-surface p-4 space-y-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium text-text-primary">Formas de pagamento *</p>
                <button type="button" wire:click="adicionarPagamento" class="text-xs font-medium text-primary hover:text-primary-light">+ Adicionar forma</button>
            </div>

            @foreach ($pagamentos as $indice => $pagamento)
                <div class="flex items-start gap-2">
                    <div class="flex-1">
                        <select wire:model="pagamentos.{{ $indice }}.forma" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @foreach ($formasPagamentoDisponiveis as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error("pagamentos.{$indice}.forma") <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-36">
                        <input type="number" step="0.01" placeholder="Valor (R$)" wire:model="pagamentos.{{ $indice }}.valor" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                        @error("pagamentos.{$indice}.valor") <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1">
                        <input type="text" placeholder="Observação (opcional)" wire:model="pagamentos.{{ $indice }}.observacao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                    @if (count($pagamentos) > 1)
                        <button type="button" wire:click="removerPagamento({{ $indice }})" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-control text-error hover:bg-error/5" title="Remover">
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    @endif
                </div>
            @endforeach

            <p class="text-xs {{ abs($this->totalPagamentos() - $this->totalVenda()) > 0.01 ? 'text-error' : 'text-text-secondary' }}">
                Soma das formas: R$ {{ number_format($this->totalPagamentos(), 2, ',', '.') }} — valor da venda: R$ {{ number_format($this->totalVenda(), 2, ',', '.') }}
            </p>
            @error('pagamentos') <p class="text-xs text-error">{{ $message }}</p> @enderror
        </div>

        @if ($this->temPagamentoTroca())
            <div class="rounded-control border border-border bg-surface p-4 space-y-3">
                <p class="text-xs font-medium text-text-primary">Veículo dado como troca</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Marca *</label>
                        <input type="text" wire:model="troca_marca" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('troca_marca') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Modelo *</label>
                        <input type="text" wire:model="troca_modelo" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('troca_modelo') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Ano modelo</label>
                        <input type="number" wire:model="troca_ano_modelo" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Placa</label>
                        <input type="text" wire:model="troca_placa" maxlength="8" class="w-full rounded-control border-border text-sm uppercase focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">KM</label>
                        <input type="number" wire:model="troca_km" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Valor de volta (avaliação) *</label>
                    <input type="number" step="0.01" wire:model="troca_valor_avaliado" class="w-full max-w-[220px] rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                    @error('troca_valor_avaliado') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        {{-- Lucro de financiamento/cartão e impostos - entram no DRE como linhas proprias --}}
        <div class="rounded-control border border-border bg-surface p-4 space-y-3">
            <p class="text-xs font-medium text-text-primary">Financeiro da venda</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Lucro financiamento / cartão (R$)</label>
                    <input type="number" step="0.01" wire:model="lucro_financeiro" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                    @error('lucro_financeiro') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Banco / financeira / operadora</label>
                    <input type="text" wire:model="instituicao_financeira" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-text-secondary mb-1">Impostos (R$)</label>
                <input type="number" step="0.01" wire:model="impostos" class="w-full max-w-[220px] rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                @error('impostos') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Garantia (dias)</label>
            <input type="number" wire:model="prazo_garantia_dias" class="w-full max-w-[150px] rounded-control border-border text-sm focus:border-primary focus:ring-primary">
        </div>

        <div>
            <label class="block text-xs font-medium text-text-secondary mb-1">Status inicial</label>
            <select wire:model="status" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <option value="pendente">Pendente — ainda depende de aprovação/financiamento</option>
                <option value="confirmada">Confirmada — fecha a venda agora (reserva o veículo)</option>
            </select>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                {{ $status === 'confirmada' ? 'Registrar e confirmar venda' : 'Registrar venda pendente' }}
            </button>
            <a href="{{ route('admin.vendas.index') }}" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</a>
        </div>
    </form>
</div>
