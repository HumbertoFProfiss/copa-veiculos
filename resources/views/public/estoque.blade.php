<x-layouts.public :title="'Estoque - '.config('app.name')">
    <div class="max-w-7xl mx-auto px-6 py-10">
        <h1 class="text-2xl font-semibold text-text-primary mb-6">Estoque</h1>

        <form method="GET" class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-8 bg-surface border border-border rounded-card p-4">
            <select name="marca" class="rounded-control border-border text-sm">
                <option value="">Marca</option>
                @foreach ($marcasDisponiveis as $marca)
                    <option value="{{ $marca }}" @selected(request('marca') === $marca)>{{ $marca }}</option>
                @endforeach
            </select>
            <input type="text" name="modelo" value="{{ request('modelo') }}" placeholder="Modelo" class="rounded-control border-border text-sm">
            <select name="cambio" class="rounded-control border-border text-sm">
                <option value="">Câmbio</option>
                <option value="Manual" @selected(request('cambio') === 'Manual')>Manual</option>
                <option value="Automático" @selected(request('cambio') === 'Automático')>Automático</option>
            </select>
            <select name="combustivel" class="rounded-control border-border text-sm">
                <option value="">Combustível</option>
                <option value="Flex" @selected(request('combustivel') === 'Flex')>Flex</option>
                <option value="Gasolina" @selected(request('combustivel') === 'Gasolina')>Gasolina</option>
                <option value="Diesel" @selected(request('combustivel') === 'Diesel')>Diesel</option>
            </select>
            <input type="number" name="preco_min" value="{{ request('preco_min') }}" placeholder="Preço mín." class="rounded-control border-border text-sm">
            <input type="number" name="preco_max" value="{{ request('preco_max') }}" placeholder="Preço máx." class="rounded-control border-border text-sm">
            <button type="submit" class="rounded-control bg-primary text-white text-sm font-medium hover:bg-primary-light">Filtrar</button>
        </form>

        @if ($veiculos->isEmpty())
            <p class="text-text-secondary text-center py-16">Nenhum veículo encontrado com esses filtros.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($veiculos as $veiculo)
                    <x-public.veiculo-card :veiculo="$veiculo" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $veiculos->links() }}
            </div>
        @endif
    </div>
</x-layouts.public>
