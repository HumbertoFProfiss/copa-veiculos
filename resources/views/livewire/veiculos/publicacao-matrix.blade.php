<div>
    <div class="flex items-center justify-between mb-4">
        <p class="text-xs text-text-secondary">Selecione onde publicar - a publicação acontece na hora, sem sair desta tela.</p>
        <div class="flex items-center gap-3">
            <button wire:click="selecionarTodas" type="button" class="text-xs text-primary hover:underline">Selecionar todas</button>
            <button wire:click="nenhuma" type="button" class="text-xs text-text-secondary hover:underline">Nenhuma</button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach ($canais as $canal)
            @php $publicacao = $publicacoesPorCanal->get($canal->id); @endphp
            <div wire:key="canal-{{ $canal->id }}"
                 class="flex items-center justify-between p-3 rounded-control border {{ $publicacao?->status === 'publicado' ? 'border-primary bg-primary-soft' : 'border-border' }}">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           wire:click="togglar({{ $canal->id }})"
                           @checked($publicacao?->status === 'publicado')
                           wire:loading.attr="disabled" wire:target="togglar({{ $canal->id }}),selecionarTodas,nenhuma"
                           class="rounded-control border-border text-primary focus:ring-primary">
                    <span class="text-sm text-text-primary">{{ $canal->nome }}</span>
                </label>

                <div class="flex items-center gap-2">
                    @if ($publicacao)
                        @if ($publicacao->status === 'publicado')
                            <x-admin.status-badge variant="success" label="Publicado" />
                        @elseif ($publicacao->status === 'erro')
                            <span title="{{ $publicacao->ultimo_erro }}">
                                <x-admin.status-badge variant="error" label="Erro" />
                            </span>
                            <button wire:click="reprocessar({{ $canal->id }})" type="button" title="Reprocessar" class="text-text-secondary hover:text-primary">
                                <x-heroicon-o-arrow-path class="w-4 h-4" />
                            </button>
                        @elseif ($publicacao->status === 'despublicado')
                            <x-admin.status-badge variant="neutral" label="Removido" />
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
