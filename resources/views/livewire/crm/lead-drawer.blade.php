<div class="p-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h2 class="text-lg font-semibold text-text-primary">{{ $lead->nome }}</h2>
            <x-admin.status-badge variant="info" :label="$lead->estagioLabel()" />
        </div>
        <button wire:click="fechar" type="button" class="text-text-secondary hover:text-text-primary">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <div class="space-y-1 text-sm text-text-secondary mb-6">
        @if ($lead->telefone)<div class="flex items-center gap-2"><x-heroicon-o-phone class="w-4 h-4" /> {{ $lead->telefone }}</div>@endif
        @if ($lead->email)<div class="flex items-center gap-2"><x-heroicon-o-envelope class="w-4 h-4" /> {{ $lead->email }}</div>@endif
        @if ($lead->veiculo)
            <div class="flex items-center gap-2">
                <x-heroicon-o-truck class="w-4 h-4" />
                <a href="{{ route('admin.veiculos.editar', $lead->veiculo) }}" class="text-primary hover:underline">{{ $lead->veiculo->marca }} {{ $lead->veiculo->modelo }}</a>
            </div>
        @endif
        <div class="flex items-center gap-2"><x-heroicon-o-globe-alt class="w-4 h-4" /> Origem: {{ $lead->origem }}</div>
    </div>

    <div class="mb-6">
        <label class="block text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Mudar estágio</label>
        <div class="flex flex-wrap gap-2">
            @foreach (\App\Models\Lead::ESTAGIO_LABELS as $estagio => $label)
                <button wire:click="moverEstagio('{{ $estagio }}')" type="button"
                        class="px-2.5 py-1 rounded-control text-xs border
                            {{ $lead->estagio === $estagio ? 'bg-primary text-white border-primary' : 'border-border text-text-secondary hover:bg-surface' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="mb-6">
        <label class="block text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Registrar contato</label>
        <form wire:submit="registrarContato" class="flex gap-2">
            <input type="text" wire:model="novaAnotacao" placeholder="O que foi conversado?"
                   class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <button type="submit" class="px-3 py-2 rounded-control bg-surface border border-border text-sm hover:bg-primary-soft">
                <x-heroicon-o-plus class="w-4 h-4" />
            </button>
        </form>
    </div>

    <div class="mb-6">
        <label class="block text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Tarefas</label>
        <form wire:submit="adicionarTarefa" class="flex gap-2 mb-3">
            <input type="text" wire:model="novaTarefaTitulo" placeholder="Nova tarefa"
                   class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <input type="datetime-local" wire:model="novaTarefaVencimento"
                   class="rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <button type="submit" class="px-3 py-2 rounded-control bg-surface border border-border text-sm hover:bg-primary-soft">
                <x-heroicon-o-plus class="w-4 h-4" />
            </button>
        </form>
        @error('novaTarefaTitulo') <p class="text-xs text-error mb-2">{{ $message }}</p> @enderror
        <div class="space-y-2">
            @forelse ($lead->tarefas as $tarefa)
                <div class="flex items-center gap-2 text-sm">
                    <input type="checkbox" @checked($tarefa->concluida) wire:click="concluirTarefa({{ $tarefa->id }})" @disabled($tarefa->concluida)
                           class="rounded-control border-border text-primary focus:ring-primary">
                    <span class="{{ $tarefa->concluida ? 'line-through text-text-secondary' : 'text-text-primary' }}">{{ $tarefa->titulo }}</span>
                    <span class="text-xs text-text-secondary ml-auto">{{ $tarefa->vencimento_em->format('d/m H:i') }}</span>
                </div>
            @empty
                <p class="text-xs text-text-secondary">Nenhuma tarefa.</p>
            @endforelse
        </div>
    </div>

    <div>
        <label class="block text-xs font-medium text-text-secondary uppercase tracking-wide mb-2">Histórico</label>
        <div class="space-y-3">
            @forelse ($lead->historico as $item)
                <div class="text-sm">
                    <p class="text-text-primary">{{ $item->detalhes }}</p>
                    <p class="text-xs text-text-secondary">{{ $item->usuario?->name ?? 'Sistema' }} &middot; {{ $item->created_at->diffForHumans() }}</p>
                </div>
            @empty
                <p class="text-xs text-text-secondary">Sem histórico ainda.</p>
            @endforelse
        </div>
    </div>
</div>
