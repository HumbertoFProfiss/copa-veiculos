@props(['coluna', 'ordenarPor', 'ordenarDirecao'])

<th class="px-4 py-3 text-left text-xs font-semibold text-text-secondary uppercase tracking-wide cursor-pointer select-none"
    wire:click="ordenar('{{ $coluna }}')">
    <span class="inline-flex items-center gap-1">
        {{ $slot }}
        @if ($ordenarPor === $coluna)
            @if ($ordenarDirecao === 'asc')
                <x-heroicon-o-chevron-up class="w-3 h-3" />
            @else
                <x-heroicon-o-chevron-down class="w-3 h-3" />
            @endif
        @endif
    </span>
</th>
