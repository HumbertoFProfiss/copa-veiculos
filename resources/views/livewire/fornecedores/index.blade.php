<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Fornecedores</h1>
        @can('fornecedores.criar')
            <button wire:click="novo" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Novo fornecedor
            </button>
        @endcan
    </div>

    <div class="relative mb-4 max-w-sm">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome..."
               class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <x-admin.th-sort coluna="nome" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Nome</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Tipo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Contato</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($fornecedores as $fornecedor)
            <tr wire:key="fornecedor-{{ $fornecedor->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">{{ $fornecedor->nome }}</td>
                <td class="px-4 py-3 text-text-secondary">{{ $fornecedor->tipoLabel() }}</td>
                <td class="px-4 py-3 text-text-secondary">
                    <div>{{ $fornecedor->telefone ?: '—' }}</div>
                    <div class="text-xs">{{ $fornecedor->email ?: '' }}</div>
                </td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$fornecedor->ativo ? 'success' : 'neutral'" :label="$fornecedor->ativo ? 'Ativo' : 'Inativo'" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('fornecedores.editar')
                            <button wire:click="editar({{ $fornecedor->id }})" type="button" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                        @endcan
                        @can('fornecedores.excluir')
                            <button wire:click="excluir({{ $fornecedor->id }})" wire:confirm="Excluir este fornecedor?" type="button" class="text-text-secondary hover:text-error">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum fornecedor encontrado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $fornecedores->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">{{ $editandoId ? 'Editar fornecedor' : 'Novo fornecedor' }}</h2>
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
                            @foreach ($tipos as $valor => $label)
                                <option value="{{ $valor }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">CPF/CNPJ</label>
                            <input type="text" wire:model="cpf_cnpj" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Telefone</label>
                            <input type="text" wire:model="telefone" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">E-mail</label>
                        <input type="email" wire:model="email" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                        <input type="checkbox" wire:model="ativo" class="rounded-control border-border text-primary focus:ring-primary"> Ativo
                    </label>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Salvar</button>
                        <button type="button" wire:click="$set('mostrarForm', false)" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
