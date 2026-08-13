<div>
    <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Sua conta</p>
    <h1 class="font-heading text-2xl font-semibold text-brand-900 dark:text-white mb-1">Favoritos</h1>
    <p class="text-sm text-muted dark:text-duskmuted mb-8">Veículos que você salvou</p>

    @if ($favoritos->isEmpty())
        <div class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-10 text-center">
            <x-heroicon-o-heart class="w-10 h-10 text-brand-100 mx-auto mb-3" />
            <p class="text-muted dark:text-duskmuted">Você ainda não favoritou nenhum veículo.</p>
            <a href="{{ route('estoque') }}" class="inline-block mt-4 text-brand-700 font-medium hover:underline text-sm">Ver estoque</a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($favoritos as $favorito)
                @php $veiculo = $favorito->veiculo; @endphp
                <div class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard overflow-hidden">
                    <a href="{{ route('veiculo.show', $veiculo) }}" class="block aspect-[4/3] bg-brand-100/50 dark:bg-duskcard/60">
                        @if ($veiculo->fotos->isNotEmpty())
                            <img src="{{ $veiculo->fotos->first()->url() }}" alt="{{ $veiculo->marca }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-muted dark:text-duskmuted">
                                <x-heroicon-o-photo class="w-10 h-10" />
                            </div>
                        @endif
                    </a>
                    <div class="p-5">
                        <a href="{{ route('veiculo.show', $veiculo) }}" class="font-heading font-medium text-brand-900 dark:text-white text-sm hover:text-brand-700">{{ $veiculo->marca }} {{ $veiculo->modelo }}</a>
                        <p class="font-heading text-brand-700 font-semibold mt-1 tabular-nums">
                            @if ($veiculo->preco_venda) R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }} @else Consulte @endif
                        </p>
                        <button wire:click="remover({{ $favorito->id }})" wire:confirm="Remover este veículo dos favoritos?"
                                class="mt-3 text-xs text-muted dark:text-duskmuted hover:text-red-500 flex items-center gap-1">
                            <x-heroicon-o-trash class="w-3.5 h-3.5" /> Remover
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
