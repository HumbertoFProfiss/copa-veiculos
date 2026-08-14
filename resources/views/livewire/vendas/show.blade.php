<div class="max-w-4xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.vendas.index') }}" class="text-text-secondary hover:text-text-primary">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <h1 class="text-xl font-semibold text-text-primary flex-1 min-w-0">
            {{ $venda->veiculo?->marca ?? 'Veículo removido' }} {{ $venda->veiculo?->modelo }} — Venda #{{ $venda->id }}
        </h1>
        <x-admin.status-badge :variant="match($venda->status) { 'confirmada' => 'success', 'cancelada' => 'error', default => 'warning' }" :label="ucfirst($venda->status)" />

        @can('vendas.criar')
            @if ($venda->status === 'pendente')
                <button wire:click="confirmarVenda" wire:confirm="Confirmar essa venda? O veículo vai ficar marcado como vendido e anúncios ativos serão despublicados."
                        type="button" class="px-3 py-1.5 rounded-control bg-success text-white text-xs font-medium hover:opacity-90">
                    Confirmar venda
                </button>
            @endif
            @if ($venda->status !== 'cancelada')
                <button wire:click="cancelarVenda" wire:confirm="Cancelar essa venda?{{ $venda->status === 'confirmada' ? ' O veículo volta a ficar disponível no estoque.' : '' }}"
                        type="button" class="px-3 py-1.5 rounded-control border border-error/30 text-error text-xs font-medium hover:bg-error/10">
                    Cancelar venda
                </button>
            @endif
        @endcan
    </div>

    @if (session('sucesso'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-bg border border-border rounded-card p-4">
            <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Cliente</div>
            <div class="text-sm font-medium text-text-primary">{{ $venda->cliente->nome }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Vendedor</div>
            <div class="text-sm font-medium text-text-primary">{{ $venda->vendedor->name }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Forma de pagamento</div>
            <div class="text-sm font-medium text-text-primary">{{ ucfirst($venda->forma_pagamento) }}</div>
        </div>
        <div class="bg-bg border border-border rounded-card p-4">
            <div class="text-xs text-text-secondary uppercase tracking-wide mb-1">Data da venda</div>
            <div class="text-sm font-medium text-text-primary">{{ $venda->data_venda->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="bg-bg border border-border rounded-card p-5 mb-6">
        <h2 class="text-sm font-semibold text-text-primary mb-4">Resultado financeiro do veículo</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-text-secondary text-xs mb-0.5">Preço de venda</div>
                <div class="font-medium text-text-primary tabular-nums">R$ {{ number_format($venda->preco_venda, 2, ',', '.') }}</div>
            </div>
            @if ($venda->desconto > 0)
                <div>
                    <div class="text-text-secondary text-xs mb-0.5">Desconto</div>
                    <div class="font-medium text-error tabular-nums">- R$ {{ number_format($venda->desconto, 2, ',', '.') }}</div>
                </div>
            @endif
            <div>
                <div class="text-text-secondary text-xs mb-0.5">Preço de compra</div>
                <div class="font-medium text-text-primary tabular-nums">R$ {{ number_format($venda->veiculo?->preco_compra ?? 0, 2, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-text-secondary text-xs mb-0.5">Custos do veículo</div>
                <div class="font-medium text-text-primary tabular-nums">R$ {{ number_format($venda->veiculo?->custos->sum('valor') ?? 0, 2, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-text-secondary text-xs mb-0.5">Custos de garantia (confirmados)</div>
                <div class="font-medium text-text-primary tabular-nums">R$ {{ number_format($venda->veiculo?->custosGarantiaConfirmados() ?? 0, 2, ',', '.') }}</div>
            </div>
            <div>
                <div class="text-text-secondary text-xs mb-0.5">Comissão do vendedor</div>
                <div class="font-medium text-text-primary tabular-nums">R$ {{ number_format($venda->comissao_vendedor ?? 0, 2, ',', '.') }}</div>
            </div>
        </div>
        @if ($venda->veiculo && $venda->veiculo->margem() !== null)
            <div class="mt-4 pt-4 border-t border-border">
                <span class="text-sm text-text-secondary">Margem líquida do veículo:</span>
                <span class="ml-2 text-lg font-semibold tabular-nums {{ $venda->veiculo->margem() >= 0 ? 'text-success' : 'text-error' }}">
                    R$ {{ number_format($venda->veiculo->margem(), 2, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

    @if ($venda->parcelas->isNotEmpty())
        <div class="bg-bg border border-border rounded-card p-5 mb-6">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Parcelas do financiamento</h2>
            <div class="space-y-2">
                @foreach ($venda->parcelas as $parcela)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-text-secondary">Parcela {{ $parcela->numero_parcela }} — vence {{ $parcela->vencimento->format('d/m/Y') }}</span>
                        <span class="font-medium text-text-primary tabular-nums">R$ {{ number_format($parcela->valor, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($venda->carroTroca->isNotEmpty())
        <div class="bg-bg border border-border rounded-card p-5 mb-6">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Veículo na troca</h2>
            @foreach ($venda->carroTroca as $troca)
                <p class="text-sm text-text-primary">{{ $troca->marca }} {{ $troca->modelo }} {{ $troca->ano_modelo }} — avaliado em R$ {{ number_format($troca->valor_avaliado, 2, ',', '.') }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-bg border border-border rounded-card p-5">
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-sm font-semibold text-text-primary">Garantia</h2>
            @can('vendas.criar')
                <button wire:click="novaGarantia" type="button" class="inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:underline">
                    <x-heroicon-o-plus class="w-3.5 h-3.5" /> Novo chamado
                </button>
            @endcan
        </div>
        <p class="text-xs text-text-secondary mb-4">Registre problemas cobertos por garantia — o custo de peça e serviço de chamados aprovados/concluídos entra automaticamente no resultado financeiro do veículo acima.</p>

        @forelse ($venda->garantiasChamados as $chamado)
            <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-control bg-surface mb-2">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-text-primary truncate">{{ $chamado->descricao_problema }}</p>
                    <p class="text-xs text-text-secondary">
                        Peça: R$ {{ number_format($chamado->custo_peca ?? 0, 2, ',', '.') }} · Serviço: R$ {{ number_format($chamado->custo_servico ?? 0, 2, ',', '.') }}
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <x-admin.status-badge
                        :variant="match($chamado->status) { 'aprovado', 'concluido' => 'success', 'em_analise' => 'warning', 'recusado' => 'error', default => 'neutral' }"
                        :label="$chamado->statusLabel()" />
                    @can('vendas.criar')
                        <button wire:click="editarGarantia({{ $chamado->id }})" type="button" class="text-text-secondary hover:text-primary">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                        </button>
                        <button wire:click="excluirGarantia({{ $chamado->id }})" wire:confirm="Excluir este chamado?" type="button" class="text-text-secondary hover:text-error">
                            <x-heroicon-o-trash class="w-4 h-4" />
                        </button>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-sm text-text-secondary py-4">Nenhum chamado de garantia registrado pra essa venda.</p>
        @endforelse
    </div>

    @if ($mostrarFormGarantia)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20" wire:click.self="$set('mostrarFormGarantia', false)">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">{{ $editandoGarantiaId ? 'Editar chamado' : 'Novo chamado de garantia' }}</h2>
                <form wire:submit="salvarGarantia" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Descrição do problema *</label>
                        <input type="text" wire:model="descricao_problema" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('descricao_problema') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Status *</label>
                        <select wire:model="status" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @foreach ($statusLabels as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Custo da peça (R$)</label>
                            <input type="number" step="0.01" min="0" wire:model="custo_peca" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary tabular-nums">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Custo do serviço (R$)</label>
                            <input type="number" step="0.01" min="0" wire:model="custo_servico" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary tabular-nums">
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Salvar</button>
                        <button type="button" wire:click="$set('mostrarFormGarantia', false)" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
