<?php

namespace App\Listeners;

use App\Events\LeadRecebido;
use App\Services\Webhooks\WebhookDispatcher;

class DispararWebhookLeadRecebido
{
    public function handle(LeadRecebido $event): void
    {
        $lead = $event->lead;

        app(WebhookDispatcher::class)->despachar('lead.recebido', $lead->empresa_id, [
            'evento' => 'lead.recebido',
            'lead' => [
                'id' => $lead->id,
                'nome' => $lead->nome,
                'email' => $lead->email,
                'telefone' => $lead->telefone,
                'origem' => $lead->origem,
                'portal_origem' => $lead->portal_origem,
                'estagio' => $lead->estagio,
                'veiculo_id' => $lead->veiculo_id,
                'criado_em' => $lead->created_at?->toAtomString(),
            ],
        ]);
    }
}
