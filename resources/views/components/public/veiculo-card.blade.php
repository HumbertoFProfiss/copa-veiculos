@props(['veiculo'])

<a href="{{ route('veiculo.show', $veiculo) }}" class="block bg-bg border border-border rounded-card overflow-hidden hover:border-primary transition-colors">
    <div class="aspect-[4/3] bg-surface">
        @if ($veiculo->fotos->isNotEmpty())
            <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center text-text-secondary">
                <x-heroicon-o-photo class="w-10 h-10" />
            </div>
        @endif
    </div>
    <div class="p-4">
        <h3 class="font-semibold text-text-primary">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
        <p class="text-sm text-text-secondary">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }} &middot; {{ number_format($veiculo->km, 0, ',', '.') }} km</p>
        <p class="mt-2 text-lg font-semibold text-primary tabular-nums">
            @if ($veiculo->preco_venda)
                R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
            @else
                Consulte
            @endif
        </p>
    </div>
</a>
