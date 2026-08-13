<div>
    <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Sua conta</p>
    <h1 class="font-heading text-2xl font-semibold text-brand-900 mb-1">Minhas Compras</h1>
    <p class="text-sm text-muted mb-8">Histórico de veículos comprados na loja</p>

    @if ($compras->isEmpty())
        <div class="rounded-2xl border border-brand-100 bg-white p-10 text-center">
            <x-heroicon-o-shopping-bag class="w-10 h-10 text-brand-100 mx-auto mb-3" />
            <p class="text-muted">Nenhuma compra registrada ainda.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($compras as $venda)
                <div class="rounded-2xl border border-brand-100 bg-white p-6 flex items-center justify-between gap-4 flex-wrap">
                    <div>
                        <p class="font-heading font-medium text-brand-900">{{ $venda->veiculo->marca }} {{ $venda->veiculo->modelo }}</p>
                        <p class="text-sm text-muted mt-0.5">
                            Comprado em {{ $venda->data_venda->format('d/m/Y') }}
                            @if ($venda->data_entrega)
                                &middot; Entregue em {{ $venda->data_entrega->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-heading text-brand-700 font-semibold tabular-nums">R$ {{ number_format($venda->preco_venda, 2, ',', '.') }}</span>
                        @if ($venda->status === 'confirmada')
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Confirmada</span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-100/60 text-muted">Cancelada</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
