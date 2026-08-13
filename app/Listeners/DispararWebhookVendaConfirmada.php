<?php

namespace App\Listeners;

use App\Events\VendaConfirmada;
use App\Services\Webhooks\WebhookDispatcher;

class DispararWebhookVendaConfirmada
{
    public function handle(VendaConfirmada $event): void
    {
        $venda = $event->venda;

        app(WebhookDispatcher::class)->despachar('venda.confirmada', $venda->empresa_id, [
            'evento' => 'venda.confirmada',
            'venda' => [
                'id' => $venda->id,
                'veiculo_id' => $venda->veiculo_id,
                'cliente_id' => $venda->cliente_id,
                'vendedor_id' => $venda->vendedor_id,
                'preco_venda' => $venda->preco_venda,
                'forma_pagamento' => $venda->forma_pagamento,
                'data_venda' => $venda->data_venda,
            ],
        ]);
    }
}
