<div class="max-w-5xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.veiculos.index') }}" class="text-text-secondary hover:text-text-primary">
            <x-heroicon-o-arrow-left class="w-5 h-5" />
        </a>
        <h1 class="text-xl font-semibold text-text-primary">
            {{ $veiculo ? "{$veiculo->marca} {$veiculo->modelo}" : 'Novo veículo' }}
        </h1>
        @if ($veiculo)
            <x-admin.status-badge :variant="$veiculo->statusVariant()" :label="$veiculo->statusLabel()" />
        @endif
    </div>

    <form wire:submit="salvar" class="space-y-6">
        <div class="bg-bg border border-border rounded-card p-5">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Dados do veículo</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Marca *</label>
                    <input type="text" wire:model="marca" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @error('marca') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Modelo *</label>
                    <input type="text" wire:model="modelo" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    @error('modelo') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Versão</label>
                    <input type="text" wire:model="versao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Ano fabricação</label>
                    <input type="number" wire:model="ano_fabricacao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Ano modelo</label>
                    <input type="number" wire:model="ano_modelo" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">KM *</label>
                    <input type="number" wire:model="km" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Combustível</label>
                    <input type="text" wire:model="combustivel" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Câmbio</label>
                    <input type="text" wire:model="cambio" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Cor</label>
                    <input type="text" wire:model="cor" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Portas</label>
                    <input type="number" wire:model="portas" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Motor</label>
                    <input type="text" wire:model="motor" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Status *</label>
                    <select wire:model="status" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @foreach (\App\Models\Veiculo::STATUS_LABELS as $valor => $label)
                            <option value="{{ $valor }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <label class="inline-flex items-center gap-2 mt-4 text-sm text-text-secondary">
                <input type="checkbox" wire:model="destaque" class="rounded-control border-border text-primary focus:ring-primary">
                Destacar na home do site
            </label>
        </div>

        <div class="bg-bg border border-border rounded-card p-5">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Identificação e origem</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Placa</label>
                    <input type="text" wire:model="placa" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Chassi</label>
                    <input type="text" wire:model="numero_chassi" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Renavam</label>
                    <input type="text" wire:model="renavam" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Nº de estoque</label>
                    <input type="text" wire:model="numero_estoque" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Local no pátio</label>
                    <input type="text" wire:model="local_patio" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Propriedade</label>
                    <select wire:model.live="tipo_propriedade" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        <option value="proprio">Próprio</option>
                        <option value="consignado">Consignado</option>
                        <option value="terceiro">Terceiro</option>
                    </select>
                </div>
                @if ($tipo_propriedade === 'terceiro')
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Fornecedor</label>
                        <select wire:model="fornecedor_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            <option value="">—</option>
                            @foreach ($fornecedores as $fornecedor)
                                <option value="{{ $fornecedor->id }}">{{ $fornecedor->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if ($tipo_propriedade === 'consignado')
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Consignante</label>
                        <input type="text" wire:model="consignado_nome" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Telefone do consignante</label>
                        <input type="text" wire:model="consignado_telefone" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-bg border border-border rounded-card p-5">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Preços</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Valor de compra</label>
                    <input type="number" step="0.01" wire:model="preco_compra" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Tabela FIPE</label>
                    <input type="number" step="0.01" wire:model="preco_tabela_fipe" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Valor de anúncio</label>
                    <input type="number" step="0.01" wire:model="preco_anuncio" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Valor de venda</label>
                    <input type="number" step="0.01" wire:model="preco_venda" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Valor mínimo</label>
                    <input type="number" step="0.01" wire:model="preco_minimo" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
                @if ($veiculo && $veiculo->margem() !== null)
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Margem estimada</label>
                        <div class="px-3 py-2 rounded-control bg-surface text-sm tabular-nums font-medium
                            {{ $veiculo->margem() >= 0 ? 'text-success' : 'text-error' }}">
                            R$ {{ number_format($veiculo->margem(), 2, ',', '.') }}
                        </div>
                    </div>
                @endif
            </div>

            @if ($veiculo)
                @livewire('ia.sugestao-preco', ['veiculo' => $veiculo], key('sugestao-preco-'.$veiculo->id))
            @endif
        </div>

        <div class="bg-bg border border-border rounded-card p-5">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Documentação e condição</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Situação documental</label>
                    <input type="text" wire:model="situacao_documental" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Estado de conservação</label>
                    <input type="text" wire:model="estado_conservacao" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-medium text-text-secondary mb-1">Débitos (R$)</label>
                    <input type="number" step="0.01" wire:model="debitos" class="w-full rounded-control border-border text-sm tabular-nums focus:border-primary focus:ring-primary">
                </div>
            </div>
            <div class="flex flex-wrap gap-4">
                <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                    <input type="checkbox" wire:model="chave_reserva" class="rounded-control border-border text-primary focus:ring-primary"> Chave reserva
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                    <input type="checkbox" wire:model="manual" class="rounded-control border-border text-primary focus:ring-primary"> Manual
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                    <input type="checkbox" wire:model="gravame" class="rounded-control border-border text-primary focus:ring-primary"> Gravame
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                    <input type="checkbox" wire:model="ipva_pago" class="rounded-control border-border text-primary focus:ring-primary"> IPVA pago
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                    <input type="checkbox" wire:model="licenciado" class="rounded-control border-border text-primary focus:ring-primary"> Licenciado
                </label>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-check class="w-4 h-4" />
                Salvar
            </button>
            <span wire:loading wire:target="salvar" class="text-sm text-text-secondary">Salvando...</span>
        </div>
    </form>

    @if ($veiculo)
        <div class="bg-bg border border-border rounded-card p-5 mt-6">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Fotos</h2>
            @livewire('veiculos.foto-manager', ['veiculo' => $veiculo], key('fotos-'.$veiculo->id))
        </div>

        @can('anuncios.criar')
            <div class="bg-bg border border-border rounded-card p-5 mt-6">
                <h2 class="text-sm font-semibold text-text-primary mb-4">Publicar anúncio</h2>
                @livewire('veiculos.publicacao-matrix', ['veiculo' => $veiculo], key('publicacao-'.$veiculo->id))
            </div>
        @endcan

        <div class="bg-bg border border-border rounded-card p-5 mt-6">
            <h2 class="text-sm font-semibold text-text-primary mb-4">Opcionais</h2>
            <form wire:submit="adicionarOpcional" class="flex gap-2 mb-4">
                <input type="text" wire:model="novoOpcional" placeholder="Ex: Ar condicionado"
                       class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                <button type="submit" class="px-4 py-2 rounded-control bg-surface border border-border text-sm font-medium text-text-primary hover:bg-primary-soft">
                    Adicionar
                </button>
            </form>
            <div class="flex flex-wrap gap-2">
                @foreach ($veiculo->opcionais as $opcional)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-control bg-surface border border-border text-sm text-text-primary">
                        {{ $opcional->nome }}
                        <button wire:click="removerOpcional({{ $opcional->id }})" type="button" class="text-text-secondary hover:text-error">
                            <x-heroicon-o-x-mark class="w-3 h-3" />
                        </button>
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</div>
