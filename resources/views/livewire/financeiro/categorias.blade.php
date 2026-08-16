<div>
    <x-financeiro.tabs ativo="categorias" />

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-text-primary">Categorias financeiras</h1>
            <p class="text-sm text-text-secondary mt-1">Plano de contas usado em Contas a Pagar, custos de veículo e no DRE.</p>
        </div>
        @can('financeiro.criar')
            <button wire:click="novo" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" /> Nova categoria
            </button>
        @endcan
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @foreach (['despesa' => 'Despesas', 'receita' => 'Receitas'] as $tipoValor => $tipoLabel)
            <div class="bg-bg border border-border rounded-card p-5">
                <h2 class="text-sm font-semibold text-text-primary mb-4">{{ $tipoLabel }}</h2>
                <div class="space-y-1">
                    @forelse ($categorias->where('tipo', $tipoValor) as $categoria)
                        <div>
                            <div class="flex items-center justify-between px-2 py-1.5 rounded-control hover:bg-surface group">
                                <span class="text-sm font-medium text-text-primary">{{ $categoria->nome }}</span>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="novo({{ $categoria->id }})" type="button" title="Adicionar subcategoria" class="text-text-secondary hover:text-primary">
                                        <x-heroicon-o-plus class="w-3.5 h-3.5" />
                                    </button>
                                    <button wire:click="editar({{ $categoria->id }})" type="button" class="text-text-secondary hover:text-primary">
                                        <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                    </button>
                                    <button wire:click="excluir({{ $categoria->id }})" wire:confirm="Excluir esta categoria?" type="button" class="text-text-secondary hover:text-error">
                                        <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                            @foreach ($categoria->subcategorias as $sub)
                                <div class="flex items-center justify-between px-2 py-1.5 pl-6 rounded-control hover:bg-surface group">
                                    <span class="text-sm text-text-secondary">{{ $sub->nome }}</span>
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button wire:click="editar({{ $sub->id }})" type="button" class="text-text-secondary hover:text-primary">
                                            <x-heroicon-o-pencil-square class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="excluir({{ $sub->id }})" wire:confirm="Excluir esta subcategoria?" type="button" class="text-text-secondary hover:text-error">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-sm text-text-secondary py-4">Nenhuma categoria de {{ strtolower($tipoLabel) }} ainda.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20">
            <div class="bg-bg rounded-card border border-border w-full max-w-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">{{ $editandoId ? 'Editar categoria' : 'Nova categoria' }}</h2>
                    <button type="button" wire:click="$set('mostrarForm', false)" class="text-text-secondary hover:text-text-primary" aria-label="Fechar">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit="salvar" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Nome *</label>
                        <input type="text" wire:model="nome" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('nome') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Tipo *</label>
                        <select wire:model="tipo" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="despesa">Despesa</option>
                            <option value="receita">Receita</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Categoria pai (opcional)</label>
                        <select wire:model="categoria_pai_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="">Nenhuma (categoria principal)</option>
                            @foreach ($todasCategorias->where('tipo', $tipo)->where('id', '!=', $editandoId) as $pai)
                                <option value="{{ $pai->id }}">{{ $pai->nome }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-text-secondary mt-1">Deixe em branco pra criar uma categoria principal, ou escolha uma pra virar subcategoria dela.</p>
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
