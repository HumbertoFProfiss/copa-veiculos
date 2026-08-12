<x-layouts.public :title="$veiculo->marca.' '.$veiculo->modelo.' - '.config('app.name')">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                @if ($veiculo->fotos->isNotEmpty())
                    <div class="aspect-[4/3] bg-surface rounded-card overflow-hidden mb-3">
                        <img src="{{ $veiculo->fotos->first()->url() }}" class="w-full h-full object-cover">
                    </div>
                    @if ($veiculo->fotos->count() > 1)
                        <div class="grid grid-cols-4 gap-3">
                            @foreach ($veiculo->fotos->skip(1) as $foto)
                                <div class="aspect-[4/3] bg-surface rounded-control overflow-hidden">
                                    <img src="{{ $foto->url() }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="aspect-[4/3] bg-surface rounded-card flex items-center justify-center text-text-secondary">
                        <x-heroicon-o-photo class="w-12 h-12" />
                    </div>
                @endif

                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-text-primary mb-3">Ficha técnica</h2>
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div><dt class="text-text-secondary">Ano</dt><dd class="font-medium">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</dd></div>
                        <div><dt class="text-text-secondary">KM</dt><dd class="font-medium">{{ number_format($veiculo->km, 0, ',', '.') }}</dd></div>
                        <div><dt class="text-text-secondary">Câmbio</dt><dd class="font-medium">{{ $veiculo->cambio ?: '—' }}</dd></div>
                        <div><dt class="text-text-secondary">Combustível</dt><dd class="font-medium">{{ $veiculo->combustivel ?: '—' }}</dd></div>
                        <div><dt class="text-text-secondary">Cor</dt><dd class="font-medium">{{ $veiculo->cor ?: '—' }}</dd></div>
                        <div><dt class="text-text-secondary">Portas</dt><dd class="font-medium">{{ $veiculo->portas ?: '—' }}</dd></div>
                    </dl>
                </div>

                @if ($veiculo->opcionais->isNotEmpty())
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-text-primary mb-3">Opcionais</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($veiculo->opcionais as $opcional)
                                <span class="px-3 py-1 rounded-control bg-surface border border-border text-sm text-text-primary">{{ $opcional->nome }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="bg-bg border border-border rounded-card p-5 sticky top-6">
                    <h1 class="text-xl font-semibold text-text-primary">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h1>
                    @if ($veiculo->versao)
                        <p class="text-text-secondary">{{ $veiculo->versao }}</p>
                    @endif
                    <p class="mt-4 text-2xl font-semibold text-primary tabular-nums">
                        @if ($veiculo->preco_venda)
                            R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
                        @else
                            Consulte
                        @endif
                    </p>

                    <div class="mt-6 border-t border-border pt-6">
                        @livewire('public.interesse-form', ['veiculo' => $veiculo])
                    </div>
                </div>
            </div>
        </div>

        @if ($semelhantes->isNotEmpty())
            <div class="mt-16">
                <h2 class="text-lg font-semibold text-text-primary mb-4">Veículos semelhantes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($semelhantes as $semelhante)
                        <x-public.veiculo-card :veiculo="$semelhante" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.public>
