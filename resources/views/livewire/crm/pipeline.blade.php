<div x-data="{ arrastandoId: null }">
    <h1 class="text-xl font-semibold text-text-primary mb-6">CRM</h1>

    <div class="flex gap-4 overflow-x-auto pb-4">
        @foreach ($colunas as $estagio => $label)
            <div @dragover.prevent
                 @drop.prevent="$wire.moverEstagio(arrastandoId, '{{ $estagio }}')"
                 class="w-72 shrink-0 bg-surface rounded-card border border-border p-3">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h2 class="text-sm font-semibold text-text-primary">{{ $label }}</h2>
                    <span class="text-xs text-text-secondary tabular-nums">{{ $leadsPorEstagio->get($estagio, collect())->count() }}</span>
                </div>

                <div class="space-y-2 min-h-[80px]">
                    @foreach ($leadsPorEstagio->get($estagio, collect()) as $lead)
                        <div draggable="true"
                             @dragstart="arrastandoId = {{ $lead->id }}"
                             wire:click="abrirLead({{ $lead->id }})"
                             wire:key="lead-{{ $lead->id }}"
                             class="bg-bg border border-border rounded-control p-3 cursor-move hover:border-primary transition-colors">
                            <p class="text-sm font-medium text-text-primary">{{ $lead->nome }}</p>
                            @if ($lead->veiculo)
                                <p class="text-xs text-text-secondary mt-0.5">{{ $lead->veiculo->marca }} {{ $lead->veiculo->modelo }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-text-secondary">{{ $lead->created_at->diffForHumans() }}</span>
                                @if ($lead->telefone)
                                    <a href="https://wa.me/55{{ preg_replace('/\D/', '', $lead->telefone) }}" target="_blank"
                                       @click.stop class="text-success hover:text-success/80">
                                        <x-heroicon-o-chat-bubble-left-right class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @if ($leadSelecionadoId)
        <div class="fixed inset-0 bg-black/40 flex justify-end z-20" wire:click.self="fecharDrawer">
            <div class="w-full max-w-md bg-bg h-full overflow-y-auto shadow-xl">
                @livewire('crm.lead-drawer', ['leadId' => $leadSelecionadoId], key('drawer-'.$leadSelecionadoId))
            </div>
        </div>
    @endif
</div>
