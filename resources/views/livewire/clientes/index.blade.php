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
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20" wire:click.self="$set('mostrarForm', false)">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">{{ $editandoId ? 'Editar cliente' : 'Novo cliente' }}</h2>
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
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Salvar</button>
                        <button type="button" wire:click="$set('mostrarForm', false)" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
