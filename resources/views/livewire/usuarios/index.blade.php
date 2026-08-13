<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Usuários</h1>
        @can('usuarios.criar')
            <button wire:click="novo" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Novo usuário
            </button>
        @endcan
    </div>

    @if (session('sucesso'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif
    @error('geral') <p class="mb-4 text-sm text-error">{{ $message }}</p> @enderror

    <div class="relative mb-4 max-w-sm">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome ou e-mail..."
               class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <x-admin.th-sort coluna="name" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Nome</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">E-mail</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Papel</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($usuarios as $usuario)
            <tr wire:key="usuario-{{ $usuario->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">
                    {{ $usuario->name }}
                    @if ($usuario->id === auth()->id())
                        <span class="text-xs text-text-secondary">(você)</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-text-secondary">{{ $usuario->email }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge variant="info" :label="$usuario->roles->first()->name ?? 'Sem papel'" />
                </td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$usuario->ativo ? 'success' : 'neutral'" :label="$usuario->ativo ? 'Ativo' : 'Inativo'" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('usuarios.editar')
                            <button wire:click="editar({{ $usuario->id }})" type="button" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                        @endcan
                        @can('usuarios.excluir')
                            @if ($usuario->id !== auth()->id())
                                <button wire:click="excluir({{ $usuario->id }})" wire:confirm="Remover este usuário?" type="button" class="text-text-secondary hover:text-error">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            @endif
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhum usuário encontrado.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $usuarios->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20" wire:click.self="$set('mostrarForm', false)">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <h2 class="text-lg font-semibold text-text-primary mb-4">{{ $editandoId ? 'Editar usuário' : 'Novo usuário' }}</h2>
                <form wire:submit="salvar" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Nome *</label>
                        <input type="text" wire:model="name" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('name') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">E-mail *</label>
                        <input type="email" wire:model="email" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('email') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Telefone</label>
                        <input type="text" wire:model="telefone" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Papel *</label>
                        <select wire:model="papel" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                            @foreach ($papeis as $nomePapel)
                                <option value="{{ $nomePapel }}">{{ $nomePapel }}</option>
                            @endforeach
                        </select>
                        @error('papel') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if ($filiais->count() > 1)
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">Filial</label>
                            <select wire:model="filial_id" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                                <option value="">—</option>
                                @foreach ($filiais as $filial)
                                    <option value="{{ $filial->id }}">{{ $filial->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">
                            {{ $editandoId ? 'Nova senha (deixe em branco pra manter)' : 'Senha *' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('password') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if ($editandoId)
                        <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                            <input type="checkbox" wire:model="ativo" class="rounded-control border-border text-primary focus:ring-primary"> Ativo
                        </label>
                    @endif
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Salvar</button>
                        <button type="button" wire:click="$set('mostrarForm', false)" class="px-4 py-2 rounded-control border border-border text-sm text-text-secondary hover:bg-surface">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
