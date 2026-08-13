<div>
    <h1 class="text-2xl font-bold text-white mb-1">Minhas Compras</h1>
    <p class="text-sm text-white/50 mb-8">Histórico de veículos comprados na loja</p>

    @if ($compras->isEmpty())
        <div class="bg-night-card border border-night-border rounded-xl p-10 text-center">
            <x-heroicon-o-shopping-bag class="w-10 h-10 text-white/20 mx-auto mb-3" />
            <p class="text-white/50">Nenhuma compra registrada ainda.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($compras as $venda)
                <div class="bg-night-card border border-night-border rounded-xl p-5 flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <p class="font-semibold text-white">{{ $venda->veiculo->marca }} {{ $venda->veiculo->modelo }}</p>
                        <p class="text-sm text-white/50 mt-0.5">
                            Comprado em {{ $venda->data_venda->format('d/m/Y') }}
                            @if ($venda->data_entrega)
                                &middot; Entregue em {{ $venda->data_entrega->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gold font-bold tabular-nums">R$ {{ number_format($venda->preco_venda, 2, ',', '.') }}</span>
                        @if ($venda->status === 'confirmada')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 ring-1 ring-inset ring-green-500/20">Confirmada</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/5 text-white/40 ring-1 ring-inset ring-white/10">Cancelada</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
