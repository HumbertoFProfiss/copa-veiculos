<div>
    <p class="mb-3 text-sm font-medium tracking-wide uppercase text-brand-700">Sua conta</p>
    <h1 class="font-heading text-2xl font-semibold text-brand-900 dark:text-white mb-1">Garantias</h1>
    <p class="text-sm text-muted dark:text-duskmuted mb-8">Status da garantia dos seus veículos</p>

    @if ($compras->isEmpty())
        <div class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-10 text-center">
            <x-heroicon-o-shield-check class="w-10 h-10 text-brand-100 mx-auto mb-3" />
            <p class="text-muted dark:text-duskmuted">Nenhuma garantia ativa. Elas aparecem aqui após a confirmação da compra.</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($compras as $venda)
                <div class="rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard overflow-hidden">
                    <div class="p-6 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <p class="font-heading font-medium text-brand-900 dark:text-white">{{ $venda->veiculo->marca }} {{ $venda->veiculo->modelo }} {{ $venda->veiculo->ano_modelo }}</p>
                            <p class="text-sm text-muted dark:text-duskmuted mt-0.5">
                                Comprado em {{ $venda->data_venda->format('d/m/Y') }}
                                @if ($venda->data_entrega)
                                    &middot; Entregue em {{ $venda->data_entrega->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        @if (! $venda->data_entrega)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-brand-100/60 dark:bg-duskcard/60 text-muted dark:text-duskmuted">Aguardando entrega</span>
                        @elseif ($venda->garantia_ativa)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                Ativa
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">Expirada</span>
                        @endif
                    </div>

                    @if ($venda->garantiasChamados->isNotEmpty())
                        <div class="border-t border-brand-100 dark:border-duskborder px-6 py-5">
                            <p class="text-[11px] font-semibold text-muted dark:text-duskmuted uppercase tracking-wide mb-3">Chamados de garantia</p>
                            <div class="space-y-3">
                                @foreach ($venda->garantiasChamados as $chamado)
                                    <div class="flex items-start gap-3">
                                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-medium bg-brand-100/60 dark:bg-duskcard/60 text-brand-700">{{ $chamado->statusLabel() }}</span>
                                        <div>
                                            <p class="text-sm text-ink dark:text-brand-100">{{ $chamado->descricao_problema }}</p>
                                            <p class="text-xs text-muted dark:text-duskmuted mt-0.5">Aberto em {{ $chamado->created_at->format('d/m/Y') }}</p>
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
            <div class="mt-8 rounded-2xl border border-brand-100 dark:border-duskborder bg-white dark:bg-duskcard p-6">
                <p class="font-heading text-sm font-semibold text-brand-900 dark:text-white mb-1">Precisa acionar a garantia?</p>
                <p class="text-sm text-muted dark:text-duskmuted mb-4">Entre em contato com a loja diretamente.</p>
                <a href="{{ app('tenant')->whatsappUrl() }}?text=Olá,%20preciso%20acionar%20a%20garantia%20do%20meu%20veículo" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-full bg-brand-700 px-6 py-3 text-sm font-semibold text-white shadow-brand transition-transform hover:scale-[1.02]">
                    Falar no WhatsApp
                </a>
            </div>
        @endif
    @endif
</div>
