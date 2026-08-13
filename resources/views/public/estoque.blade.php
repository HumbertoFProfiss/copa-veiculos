<x-layouts.public :title="'Estoque - '.config('app.name')">
    <div class="max-w-7xl mx-auto px-6 sm:px-8 py-16">
        <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Catálogo</p>
        <h1 class="font-heading text-brand-900 text-3xl sm:text-4xl font-semibold mb-8">Estoque</h1>

        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-10 bg-brand-100/50 border border-brand-100 rounded-2xl p-5">
            <select name="marca" class="input">
                <option value="">Marca</option>
                @foreach ($marcasDisponiveis as $marca)
                    <option value="{{ $marca }}" @selected(request('marca') === $marca)>{{ $marca }}</option>
                @endforeach
            </select>
            <input type="text" name="modelo" value="{{ request('modelo') }}" placeholder="Modelo" class="input">
            <select name="cambio" class="input">
                <option value="">Câmbio</option>
                <option value="Manual" @selected(request('cambio') === 'Manual')>Manual</option>
                <option value="Automático" @selected(request('cambio') === 'Automático')>Automático</option>
            </select>
            <select name="combustivel" class="input">
                <option value="">Combustível</option>
                <option value="Flex" @selected(request('combustivel') === 'Flex')>Flex</option>
                <option value="Gasolina" @selected(request('combustivel') === 'Gasolina')>Gasolina</option>
                <option value="Diesel" @selected(request('combustivel') === 'Diesel')>Diesel</option>
            </select>
            <input type="number" name="preco_min" value="{{ request('preco_min') }}" placeholder="Preço mín." class="input">
            <input type="number" name="preco_max" value="{{ request('preco_max') }}" placeholder="Preço máx." class="input">
            <button type="submit" class="rounded-full bg-brand-700 text-white text-sm font-semibold shadow-brand hover:scale-[1.03] transition-transform">Filtrar</button>
        </form>

        @if ($veiculos->isEmpty())
            <p class="text-muted text-center py-16">Nenhum veículo encontrado com esses filtros.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($veiculos as $veiculo)
                    <x-public.veiculo-card :veiculo="$veiculo" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $veiculos->links() }}
            </div>
        @endif
    </div>
</x-layouts.public>
