<x-layouts.public
    :title="$veiculo->marca.' '.$veiculo->modelo.' '.$veiculo->ano_modelo.' - '.config('app.name')"
    :description="($veiculo->marca.' '.$veiculo->modelo.' '.$veiculo->ano_fabricacao.'/'.$veiculo->ano_modelo.', '.number_format($veiculo->km, 0, ',', '.').' km. '.($veiculo->preco_venda ? 'R$ '.number_format($veiculo->preco_venda, 2, ',', '.') : 'Consulte o preço').'.')"
    :og-type="'product'"
    :og-image="$veiculo->fotos->first()?->url()">

    @php
        $primeiraFoto = $veiculo->fotos->first();
    @endphp
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Vehicle',
            'name' => "{$veiculo->marca} {$veiculo->modelo} {$veiculo->versao}",
            'brand' => $veiculo->marca,
            'model' => $veiculo->modelo,
            'vehicleModelDate' => (string) $veiculo->ano_modelo,
            'productionDate' => (string) $veiculo->ano_fabricacao,
            'mileageFromOdometer' => [
                '@type' => 'QuantitativeValue',
                'value' => $veiculo->km,
                'unitCode' => 'KMT',
            ],
            'vehicleTransmission' => $veiculo->cambio,
            'fuelType' => $veiculo->combustivel,
            'color' => $veiculo->cor,
            'image' => $primeiraFoto?->url(),
            'offers' => $veiculo->preco_venda ? [
                '@type' => 'Offer',
                'priceCurrency' => 'BRL',
                'price' => (float) $veiculo->preco_venda,
                'availability' => 'https://schema.org/InStock',
                'url' => route('veiculo.show', $veiculo),
            ] : null,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2">
                @if ($veiculo->fotos->isNotEmpty())
                    <div class="relative aspect-[4/3] bg-brand-100/50 dark:bg-duskcard/40 rounded-2xl overflow-hidden mb-3 shadow-brand">
                        <img src="{{ $veiculo->fotos->first()->url() }}" class="w-full h-full object-cover">
                        <div class="absolute top-3 right-3">
                            @livewire('cliente.favorito-botao', ['veiculo' => $veiculo])
                        </div>
                    </div>
                    @if ($veiculo->fotos->count() > 1)
                        <div class="grid grid-cols-4 gap-3">
                            @foreach ($veiculo->fotos->skip(1) as $foto)
                                <div class="aspect-[4/3] bg-brand-100/50 dark:bg-duskcard/40 rounded-xl overflow-hidden">
                                    <img src="{{ $foto->url() }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="aspect-[4/3] bg-brand-100/50 dark:bg-duskcard/40 rounded-2xl flex items-center justify-center text-muted dark:text-duskmuted">
                        <x-heroicon-o-photo class="w-12 h-12" />
                    </div>
                @endif

                <div class="mt-10">
                    <h2 class="font-heading text-lg font-medium text-brand-900 dark:text-white mb-4">Ficha técnica</h2>
                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                        <div><dt class="text-muted dark:text-duskmuted">Ano</dt><dd class="font-medium text-brand-900 dark:text-white">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</dd></div>
                        <div><dt class="text-muted dark:text-duskmuted">KM</dt><dd class="font-medium text-brand-900 dark:text-white">{{ number_format($veiculo->km, 0, ',', '.') }}</dd></div>
                        <div><dt class="text-muted dark:text-duskmuted">Câmbio</dt><dd class="font-medium text-brand-900 dark:text-white">{{ $veiculo->cambio ?: '—' }}</dd></div>
                        <div><dt class="text-muted dark:text-duskmuted">Combustível</dt><dd class="font-medium text-brand-900 dark:text-white">{{ $veiculo->combustivel ?: '—' }}</dd></div>
                        <div><dt class="text-muted dark:text-duskmuted">Cor</dt><dd class="font-medium text-brand-900 dark:text-white">{{ $veiculo->cor ?: '—' }}</dd></div>
                        <div><dt class="text-muted dark:text-duskmuted">Portas</dt><dd class="font-medium text-brand-900 dark:text-white">{{ $veiculo->portas ?: '—' }}</dd></div>
                    </dl>
                </div>

                @if ($veiculo->opcionais->isNotEmpty())
                    <div class="mt-10">
                        <h2 class="font-heading text-lg font-medium text-brand-900 dark:text-white mb-4">Opcionais</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($veiculo->opcionais as $opcional)
                                <span class="px-3 py-1.5 rounded-full bg-brand-100/50 dark:bg-duskcard/40 border border-brand-100 dark:border-duskborder text-sm text-brand-900 dark:text-white">{{ $opcional->nome }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-6 shadow-brand sticky top-24">
                    <h1 class="font-heading text-xl font-semibold text-brand-900 dark:text-white">{{ $veiculo->marca }} {{ $veiculo->modelo }}</h1>
                    @if ($veiculo->versao)
                        <p class="text-muted dark:text-duskmuted">{{ $veiculo->versao }}</p>
                    @endif
                    <p class="mt-4 font-heading text-2xl font-semibold text-brand-700 tabular-nums">
                        @if ($veiculo->preco_venda)
                            R$ {{ number_format($veiculo->preco_venda, 2, ',', '.') }}
                        @else
                            Consulte
                        @endif
                    </p>

                    <div class="mt-6 border-t border-brand-100 dark:border-duskborder pt-6">
                        @livewire('public.interesse-form', ['veiculo' => $veiculo])
                    </div>
                </div>
            </div>
        </div>

        @if ($semelhantes->isNotEmpty())
            <div class="mt-20">
                <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Você também pode gostar</p>
                <h2 class="font-heading text-2xl font-semibold text-brand-900 dark:text-white mb-6">Veículos semelhantes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($semelhantes as $semelhante)
                        <x-public.veiculo-card :veiculo="$semelhante" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.public>
