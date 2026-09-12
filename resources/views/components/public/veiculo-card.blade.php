@props(['veiculo'])

<a href="{{ route('veiculo.show', $veiculo) }}" class="group block rounded-2xl border border-brand-100 bg-brand-100 overflow-hidden transition-colors hover:border-brand-500">
    <div class="relative aspect-[4/3] bg-white/40 overflow-hidden">
        @if ($veiculo->fotos->isNotEmpty())
            <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }} {{ $veiculo->modelo }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center text-muted">
                <x-heroicon-o-photo class="w-10 h-10" />
            </div>
        @endif
        <div class="absolute top-3 right-3" onclick="event.preventDefault()">
            @livewire('cliente.favorito-botao', ['veiculo' => $veiculo], key('fav-card-'.$veiculo->id))
        </div>
    </div>
    <div class="p-5">
        <h3 class="font-heading font-medium text-brand-900">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h3>
        <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-brand-700 text-white text-xs font-medium">
            {{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }} &middot; {{ number_format($veiculo->km, 0, ',', '.') }} km
        </span>
        <p class="mt-3 font-heading text-lg font-semibold text-brand-700 tabular-nums">
            @if ($veiculo->preco_venda)
                R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
            @else
                Consulte
            @endif
        </p>
    </div>
</a>
