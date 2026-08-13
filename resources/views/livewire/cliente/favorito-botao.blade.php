<button wire:click.prevent.stop="alternar" type="button"
        title="{{ $favoritado ? 'Remover dos favoritos' : 'Favoritar' }}"
        class="w-9 h-9 rounded-full bg-black/50 backdrop-blur flex items-center justify-center transition-colors hover:bg-black/70">
    @if ($favoritado)
        <x-heroicon-s-heart class="w-5 h-5 text-gold" />
    @else
        <x-heroicon-o-heart class="w-5 h-5 text-white" />
    @endif
</button>
