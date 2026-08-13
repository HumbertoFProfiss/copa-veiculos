<div>
    <h1 class="text-xl font-semibold text-text-primary mb-1">Integrações</h1>
    <p class="text-sm text-text-secondary mb-6">Tokens de API pra consumir a API REST e webhooks pra receber eventos em tempo real num sistema externo.</p>

    @if (session('sucesso'))
        <div class="mb-4 flex items-center gap-2.5 px-4 py-3 rounded-card bg-success/10 text-success text-sm border border-success/20">
            <x-heroicon-o-check-circle class="w-5 h-5 shrink-0" />
            {{ session('sucesso') }}
        </div>
    @endif

    {{-- Tokens de API --}}
    <div class="bg-bg border border-border rounded-card p-5 shadow-soft mb-6">
        <h2 class="text-sm font-semibold text-text-primary mb-1">Tokens de API</h2>
        <p class="text-xs text-text-secondary mb-4">
            Usados como <code class="px-1 py-0.5 rounded bg-surface">Authorization: Bearer &lt;token&gt;</code> nas chamadas pra <code class="px-1 py-0.5 rounded bg-surface">/api/v1/...</code>.
        </p>

        @if ($tokenGerado)
            <div class="mb-4 p-4 rounded-card bg-warning/10 border border-warning/30">
                <p class="text-sm font-semibold text-text-primary mb-1">Copie o token agora — ele não é mostrado de novo.</p>
                <code class="block bg-bg border border-border rounded-control p-3 text-xs break-all">{{ $tokenGerado }}</code>
                <button wire:click="fecharTokenGerado" type="button" class="mt-2 text-sm text-primary hover:underline">Fechar</button>
            </div>
        @endif

        <form wire:submit="criarToken" class="flex gap-2 mb-4">
            <input type="text" wire:model="novoTokenNome" placeholder="Nome do token (ex: ERP interno)"
                   class="flex-1 rounded-control border-border text-sm focus:border-primary focus:ring-primary">
            <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light whitespace-nowrap">
                Gerar token
            </button>
        </form>
        @error('novoTokenNome') <p class="text-xs text-error -mt-3 mb-3">{{ $message }}</p> @enderror

        <div class="divide-y divide-border">
            @forelse ($tokens as $token)
                <div class="flex items-center justify-between py-2.5">
                    <div>
                        <div class="text-sm font-medium text-text-primary">{{ $token->name }}</div>
                        <div class="text-xs text-text-secondary">
                            {{ $token->last_used_at ? 'Usado pela última vez '.$token->last_used_at->diffForHumans() : 'Nunca usado' }}
                            · criado {{ $token->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <button wire:click="revogarToken({{ $token->id }})" wire:confirm="Revogar esse token? Sistemas que o usam param de funcionar."
                            type="button" class="text-sm text-error hover:underline">
                        Revogar
                    </button>
                </div>
            @empty
                <p class="text-sm text-text-secondary py-2">Nenhum token criado ainda.</p>
            @endforelse
        </div>
    </div>

    {{-- Webhooks --}}
    <div class="bg-bg border border-border rounded-card p-5 shadow-soft">
        <h2 class="text-sm font-semibold text-text-primary mb-1">Webhooks</h2>
        <p class="text-xs text-text-secondary mb-4">Envia um POST assinado (header <code class="px-1 py-0.5 rounded bg-surface">X-Copa-Signature</code>) pra sua URL sempre que um evento marcado acontecer.</p>

        <form wire:submit="salvarWebhook" class="mb-5 p-4 rounded-control bg-surface">
            <label class="block text-xs font-medium text-text-secondary mb-1">URL de destino</label>
            <input type="text" wire:model="webhookUrl" placeholder="https://seusistema.com/webhooks/copa"
                   class="w-full rounded-control border-border text-sm focus:border-primary focus:ring-primary mb-3">
            @error('webhookUrl') <p class="text-xs text-error -mt-2 mb-3">{{ $message }}</p> @enderror

            <label class="block text-xs font-medium text-text-secondary mb-1">Eventos</label>
            <div class="flex flex-wrap gap-4 mb-3">
                @foreach ($eventosDisponiveis as $slug => $label)
                    <label class="inline-flex items-center gap-2 text-sm text-text-secondary">
                        <input type="checkbox" wire:model="webhookEventos" value="{{ $slug }}"
                               class="rounded-control border-border text-primary focus:ring-primary">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
            @error('webhookEventos') <p class="text-xs text-error mb-3">{{ $message }}</p> @enderror

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">
                    {{ $editandoWebhookId ? 'Salvar alterações' : 'Adicionar webhook' }}
                </button>
                @if ($editandoWebhookId)
                    <button type="button" wire:click="editarWebhook" class="text-sm text-text-secondary hover:text-text-primary">Cancelar</button>
                @endif
            </div>
        </form>

        <div class="space-y-4">
            @forelse ($webhooks as $webhook)
                <div class="border border-border rounded-control p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-text-primary break-all">{{ $webhook->url }}</div>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach ($webhook->eventos ?? [] as $evento)
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-primary-soft text-primary">{{ $eventosDisponiveis[$evento] ?? $evento }}</span>
                                @endforeach
                            </div>
                        </div>
                        <x-admin.status-badge :variant="$webhook->ativo ? 'success' : 'neutral'" :label="$webhook->ativo ? 'Ativo' : 'Pausado'" />
                    </div>

                    <div class="flex flex-wrap items-center gap-3 mt-3 text-xs">
                        <button wire:click="editarWebhook({{ $webhook->id }})" type="button" class="text-primary hover:underline">Editar</button>
                        <button wire:click="alternarAtivo({{ $webhook->id }})" type="button" class="text-text-secondary hover:text-text-primary">
                            {{ $webhook->ativo ? 'Pausar' : 'Ativar' }}
                        </button>
                        <button wire:click="regenerarSecret({{ $webhook->id }})" wire:confirm="Gerar novo secret? Assinaturas antigas param de bater."
                                type="button" class="text-text-secondary hover:text-text-primary">Regenerar secret</button>
                        <button wire:click="excluirWebhook({{ $webhook->id }})" wire:confirm="Remover esse webhook?"
                                type="button" class="text-error hover:underline">Remover</button>
                    </div>

                    @if ($webhook->entregas->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-border">
                            <div class="text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wide mb-2">Últimas entregas</div>
                            <div class="space-y-1">
                                @foreach ($webhook->entregas as $entrega)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-text-secondary">
                                            {{ $eventosDisponiveis[$entrega->evento] ?? $entrega->evento }} · {{ $entrega->created_at->diffForHumans() }}
                                            @if ($entrega->resposta_http) · HTTP {{ $entrega->resposta_http }} @endif
                                        </span>
                                        <span class="flex items-center gap-2">
                                            <x-admin.status-badge
                                                :variant="match($entrega->status) { 'sucesso' => 'success', 'falhou' => 'error', default => 'warning' }"
                                                :label="ucfirst($entrega->status)" />
                                            @if ($entrega->status === 'falhou')
                                                <button wire:click="reenviarEntrega({{ $entrega->id }})" type="button" class="text-primary hover:underline">Reenviar</button>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-text-secondary">Nenhum webhook configurado ainda.</p>
            @endforelse
        </div>
    </div>
</div>
