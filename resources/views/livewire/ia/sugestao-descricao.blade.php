<div class="mt-3">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-xs font-semibold text-text-secondary uppercase tracking-wide">Sugestão de descrição (IA)</h3>
        @unless ($sugestaoAtual)
            <button wire:click="solicitar" wire:loading.attr="disabled" type="button" class="text-xs text-primary hover:underline disabled:opacity-60">
                <span wire:loading.remove wire:target="solicitar">Pedir sugestão</span>
                <span wire:loading wire:target="solicitar">Escrevendo...</span>
            </button>
        @endunless
    </div>

    @unless ($iaDisponivel)
        <p class="text-xs text-text-secondary">Assistente de IA não configurado (defina AI_PROVIDER/AI_API_KEY no .env).</p>
    @endunless

    @if ($sugestaoAtual)
        <div class="bg-primary-soft border border-primary/20 rounded-control p-3">
            <p class="text-sm text-text-primary whitespace-pre-line">{{ $sugestaoAtual->conteudo_sugerido }}</p>
            <div class="flex items-center gap-3 mt-3">
                <button wire:click="usar" type="button" class="text-xs font-medium text-success hover:underline">Usar essa descrição</button>
                <button wire:click="descartar" type="button" class="text-xs text-text-secondary hover:underline">Descartar</button>
            </div>
        </div>
    @endif
</div>
