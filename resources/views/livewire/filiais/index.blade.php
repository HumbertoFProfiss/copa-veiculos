<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-text-primary">Filiais</h1>
        @can('filiais.criar')
            <button wire:click="novo" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                <x-heroicon-o-plus class="w-4 h-4" />
                Nova filial
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
        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome..."
               class="w-full pl-9 pr-3 py-2 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
    </div>

    <x-admin.data-table>
        <x-slot:head>
            <x-admin.th-sort coluna="nome" :ordenar-por="$ordenarPor" :ordenar-direcao="$ordenarDirecao">Nome</x-admin.th-sort>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Cidade/UF</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Veículos</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Equipe</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
        </x-slot:head>

        @forelse ($filiais as $filial)
            <tr wire:key="filial-{{ $filial->id }}" class="hover:bg-surface">
                <td class="px-4 py-3 font-medium text-text-primary">
                    {{ $filial->nome }}
                    @if ($filial->principal)
                        <span class="ml-1 text-[11px] px-1.5 py-0.5 rounded bg-primary-soft text-primary">Principal</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-text-secondary">
                    {{ $filial->cidade ? "{$filial->cidade}/{$filial->uf}" : '—' }}
                </td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $filial->veiculos_count }}</td>
                <td class="px-4 py-3 text-text-secondary tabular-nums">{{ $filial->usuarios_count }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :variant="$filial->ativa ? 'success' : 'neutral'" :label="$filial->ativa ? 'Ativa' : 'Inativa'" />
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        @can('filiais.editar')
                            <button wire:click="editar({{ $filial->id }})" type="button" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                        @endcan
                        @can('filiais.excluir')
                            <button wire:click="excluir({{ $filial->id }})" wire:confirm="Excluir esta filial?" type="button" class="text-text-secondary hover:text-error">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-text-secondary text-sm">Nenhuma filial encontrada.</td></tr>
        @endforelse
    </x-admin.data-table>

    <div class="mt-4">{{ $filiais->links() }}</div>

    @if ($mostrarForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-20">
            <div class="bg-bg rounded-card border border-border w-full max-w-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-text-primary">{{ $editandoId ? 'Editar filial' : 'Nova filial' }}</h2>
                    <button type="button" wire:click="$set('mostrarForm', false)" class="text-text-secondary hover:text-text-primary" aria-label="Fechar">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
                <form wire:submit="salvar" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Nome *</label>
                        <input type="text" wire:model="nome" placeholder="Ex: Filial Zona Sul" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        @error('nome') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Endereço</label>
                        <input type="text" wire:model="endereco" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-text-secondary mb-1">Cidade</label>
                            <input type="text" wire:model="cidade" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary mb-1">UF</label>
                            <input type="text" wire:model="uf" maxlength="2" class="w-full rounded-control border-border text-sm uppercase focus:border-primary focus:ring-primary">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-text-secondary mb-1">Telefone</label>
                        <input type="text" wire:model="telefone" class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary">
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                        <input type="checkbox" wire:model="ativa" class="rounded-control border-border text-primary focus:ring-primary"> Ativa
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
