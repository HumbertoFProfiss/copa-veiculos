<?php

namespace App\Listeners;

use App\Events\LeadRecebido;
use App\Models\LeadHistorico;

/**
 * Registra a notificação no histórico do lead (real, testável) e inicia o
 * cronômetro de 1º atendimento (respondido_em fica null até o vendedor
 * registrar contato). Notificação in-app real; o envio por WhatsApp
 * de verdade depende do WhatsAppProvider (Cloud API) ainda não configurado -
 * fica como stub até existir credencial (ver plano §8/§Contexto).
 */
class RegistrarNotificacaoLeadRecebido
{
    public function handle(LeadRecebido $event): void
    {
        LeadHistorico::create([
            'lead_id' => $event->lead->id,
            'acao' => 'lead_recebido',
            'detalhes' => 'Lead recebido via '.($event->lead->portal_origem ?? $event->lead->origem).'. Vendedor notificado.',
        ]);
    }
}
