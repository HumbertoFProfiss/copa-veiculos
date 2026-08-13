<div>
    <h1 class="text-2xl font-bold text-white mb-1">Garantias</h1>
    <p class="text-sm text-white/50 mb-8">Status da garantia dos seus veículos</p>

    @if ($compras->isEmpty())
        <div class="bg-night-card border border-night-border rounded-xl p-10 text-center">
            <x-heroicon-o-shield-check class="w-10 h-10 text-white/20 mx-auto mb-3" />
            <p class="text-white/50">Nenhuma garantia ativa. Elas aparecem aqui após a confirmação da compra.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($compras as $venda)
                <div class="bg-night-card border border-night-border rounded-xl overflow-hidden">
                    <div class="p-5 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <p class="font-semibold text-white">{{ $venda->veiculo->marca }} {{ $venda->veiculo->modelo }} {{ $venda->veiculo->ano_modelo }}</p>
                            <p class="text-sm text-white/50 mt-0.5">
                                Comprado em {{ $venda->data_venda->format('d/m/Y') }}
                                @if ($venda->data_entrega)
                                    &middot; Entregue em {{ $venda->data_entrega->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        @if (! $venda->data_entrega)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-white/5 text-white/40 ring-1 ring-inset ring-white/10">Aguardando entrega</span>
                        @elseif ($venda->garantia_ativa)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/10 text-green-400 ring-1 ring-inset ring-green-500/20">
                                Ativa &middot; {{ $venda->garantia_dias_restantes }} dias restantes
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-500/10 text-red-400 ring-1 ring-inset ring-red-500/20">Expirada</span>
                        @endif
                    </div>

                    @if ($venda->garantiasChamados->isNotEmpty())
                        <div class="border-t border-night-border px-5 py-4">
                            <p class="text-[11px] font-semibold text-white/40 uppercase tracking-wide mb-3">Chamados de garantia</p>
                            <div class="space-y-3">
                                @foreach ($venda->garantiasChamados as $chamado)
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 px-2 py-0.5 rounded-control text-[11px] font-medium bg-gold/10 text-gold">{{ $chamado->statusLabel() }}</span>
                                        <div>
                                            <p class="text-sm text-white/80">{{ $chamado->descricao_problema }}</p>
                                            <p class="text-xs text-white/40 mt-0.5">Aberto em {{ $chamado->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if (app('tenant')?->whatsappUrl())
            <div class="mt-8 bg-night-card border border-night-border rounded-xl p-5">
                <p class="text-sm font-semibold text-white mb-1">Precisa acionar a garantia?</p>
                <p class="text-sm text-white/50 mb-4">Entre em contato com a loja diretamente.</p>
                <a href="{{ app('tenant')->whatsappUrl() }}?text=Olá,%20preciso%20acionar%20a%20garantia%20do%20meu%20veículo" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-control bg-gold text-night font-semibold text-sm hover:bg-gold-dark transition-colors">
                    Falar no WhatsApp
                </a>
            </div>
        @endif
    @endif
</div>
