<div>
    <div class="flex items-center gap-3 px-4 py-3 border-b border-border">
        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-text-secondary shrink-0" />
        <input type="text" wire:model.live.debounce.200ms="termo" x-ref="buscaGlobalInput"
               placeholder="Buscar veículos, clientes, leads, fornecedores, equipe..."
               class="flex-1 border-0 focus:ring-0 text-sm p-0 placeholder:text-text-secondary/60">
        <kbd class="hidden sm:inline text-[11px] px-1.5 py-0.5 rounded border border-border text-text-secondary">Esc</kbd>
    </div>

    <div class="max-h-96 overflow-y-auto">
        @if (mb_strlen(trim($termo)) < 2)
            <p class="px-4 py-8 text-center text-sm text-text-secondary">Digite ao menos 2 letras pra buscar.</p>
        @elseif (empty($resultados))
            <p class="px-4 py-8 text-center text-sm text-text-secondary">Nada encontrado pra "{{ $termo }}".</p>
        @else
            @foreach ($resultados as $categoria => $itens)
                <div class="px-4 pt-3 pb-1 text-[11px] font-semibold text-text-secondary/70 uppercase tracking-wide">
                    {{ $categoria }}
                </div>
                @foreach ($itens as $item)
                    <a href="{{ $item['url'] }}"
                       class="flex flex-col px-4 py-2 hover:bg-surface transition-colors">
                        <span class="text-sm text-text-primary font-medium">{{ $item['titulo'] }}</span>
                        @if ($item['subtitulo'])
                            <span class="text-xs text-text-secondary">{{ $item['subtitulo'] }}</span>
                        @endif
                    </a>
                @endforeach
            @endforeach
        @endif
    </div>
</div>
