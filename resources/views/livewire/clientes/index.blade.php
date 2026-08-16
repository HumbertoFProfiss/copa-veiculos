<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Clientes</h1>
        @can('clientes.criar')
            <button wire:click="novo" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Novo cliente
            </button>
        @endcan
    </div>

    <div class="relative mb-4 max-w-sm">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome, CPF, e-mail..."
               class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <x-admin.th-sort coluna="nome" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Nome</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">CPF</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Contato</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($clientes as $cliente)
            <tr wire:key="cliente-{{ $cliente->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $cliente->nome }}</td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $cliente->cpf ?: '—' }}</td>
                <td class="px-4 py-3 text-text-secondary">
                    <div>{{ $cliente->email ?: '—' }}</div>
                    <div class="text-xs">{{ $cliente->telefone ?: '' }}</div>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('clientes.editar')
                            <button wire:click="editar({{ $cliente->id }})" type="button" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                        @endcan
                        @can('clientes.excluir')
                            <button wire:click="excluir({{ $cliente->id }})" wire:confirm="Excluir este cliente?" type="button" class="text-text-secondary hover:text-error">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum cliente encontrado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $clientes->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20">
            <div class="bg-bg rounded-card border border-border w-full max-w-xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">{{ $editandoId ? 'Editar cliente' : 'Novo cliente' }}</h2>
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
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">CPF</label>
                            <input type="text" wire:model="cpf" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">E-mail</label>
                            <input type="email" wire:model="email" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Telefone</label>
                            <input type="text" wire:model="telefone" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">WhatsApp</label>
                            <input type="text" wire:model="whatsapp" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                    </div>

                    <div class="pt-2 border-t border-border">
                        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wide mt-3 mb-2">Endereço</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-text-secondary mb-1">CEP</label>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="cep" placeholder="00000-000" maxlength="9"
                                           class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                    <button type="button" wire:click="buscarCep" wire:loading.attr="disabled" wire:target="buscarCep"
                                            class="shrink-0 px-3 py-2 rounded-control bg-surface border border-border text-sm font-medium text-text-primary hover:bg-primary-soft disabled:opacity-60">
                                        <span wire:loading.remove wire:target="buscarCep">Buscar</span>
                                        <span wire:loading wire:target="buscarCep">...</span>
                                    </button>
                                </div>
                                @if ($consultaCepErro)
                                    <p class="text-xs text-error mt-1">{{ $consultaCepErro }}</p>
                                @endif
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text-secondary mb-1">UF</label>
                                <input type="text" wire:model="uf" maxlength="2" class="w-full rounded-control border-border text-sm uppercase focus:border-primary focus:ring-primary">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="block text-xs font-medium text-text-secondary mb-1">Endereço</label>
                                <input type="text" wire:model="endereco" placeholder="Rua, número, bairro"
                                       class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-text-secondary mb-1">Cidade</label>
                                <input type="text" wire:model="cidade" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            </div>
                        </div>
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
