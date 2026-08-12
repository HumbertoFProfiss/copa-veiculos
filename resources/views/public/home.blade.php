<x-layouts.public :title="config('app.name').' - Encontre seu carro'">
    <section class="max-w-7xl mx-auto px-6 py-16 text-center">
        <h1 class="text-3xl sm:text-4xl font-semibold text-text-primary">Aqui você encontra o carro certo</h1>
        <p class="mt-3 text-text-secondary max-w-xl mx-auto">Multimarcas, zero KM e seminovos. Encontre o carro perfeito pra você.</p>
        <a href="{{ route('estoque') }}"
           class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-control bg-primary text-white font-medium hover:bg-primary-light">
            Ver estoque completo
        </a>
    </section>

    @if ($destaques->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 py-10">
            <h2 class="text-xl font-semibold text-text-primary mb-6">Destaques</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($destaques as $veiculo)
                    <x-public.veiculo-card :veiculo="$veiculo" />
                @endforeach
            </div>
        </section>
    @endif

    @if ($ultimasAdicoes->isNotEmpty())
        <section class="max-w-7xl mx-auto px-6 py-10">
            <h2 class="text-xl font-semibold text-text-primary mb-6">Últimas adições</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($ultimasAdicoes as $veiculo)
                    <x-public.veiculo-card :veiculo="$veiculo" />
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
