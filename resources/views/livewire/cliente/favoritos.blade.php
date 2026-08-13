<div>
    <h1 class="text-2xl font-bold text-white mb-1">Favoritos</h1>
    <p class="text-sm text-white/50 mb-8">Veículos que você salvou</p>

    @if ($favoritos->isEmpty())
        <div class="bg-night-card border border-night-border rounded-xl p-10 text-center">
            <x-heroicon-o-heart class="w-10 h-10 text-white/20 mx-auto mb-3" />
            <p class="text-white/50">Você ainda não favoritou nenhum veículo.</p>
            <a href="{{ route('estoque') }}" class="inline-block mt-4 text-gold hover:underline text-sm">Ver estoque</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($favoritos as $favorito)
                @php $veiculo = $favorito->veiculo; @endphp
                <div class="bg-night-card border border-night-border rounded-xl overflow-hidden">
                    <a href="{{ route('veiculo.show', $veiculo) }}" class="block aspect-[4/3] bg-night">
                        @if ($veiculo->fotos->isNotEmpty())
                            <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-white/20">
                                <x-heroicon-o-photo class="w-10 h-10" />
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <a href="{{ route('veiculo.show', $veiculo) }}" class="font-semibold text-white text-sm hover:text-gold">{{ $veiculo->marca }} {{ $veiculo->modelo }}</a>
                        <p class="text-gold font-bold mt-1 tabular-nums">
                            @if ($veiculo->preco_venda) R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }} @else Consulte @endif
                        </p>
                        <button wire:click="remover({{ $favorito->id }})" wire:confirm="Remover este veículo dos favoritos?"
                                class="mt-3 text-xs text-white/40 hover:text-red-400 flex items-center gap-1">
                            <x-heroicon-o-trash class="w-3.5 h-3.5" /> Remover
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
