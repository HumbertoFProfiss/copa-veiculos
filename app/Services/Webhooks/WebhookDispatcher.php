<?php

namespace App\Services\Webhooks;

use App\Jobs\EntregarWebhook;
use App\Models\Webhook;
use App\Models\WebhookEntrega;

/**
 * Ponto unico pra disparar um evento pra todos os webhooks configurados
 * pela empresa que escutam aquele evento (ver Webhook::escutaEvento).
 * Cada listener de dominio (DispararWebhookLeadRecebido...) so chama isto -
 * quem sabe o formato de URL/assinatura/retry e o job EntregarWebhook.
 */
class WebhookDispatcher
{
    public function despachar(string $evento, int $empresaId, array $payload): void
    {
        Webhook::where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->get()
            ->filter(fn (Webhook $webhook) => $webhook->escutaEvento($evento))
            ->each(function (Webhook $webhook) use ($evento, $payload) {
                $entrega = WebhookEntrega::create([
                    'webhook_id' => $webhook->id,
                    'evento' => $evento,
                    'payload' => $payload,
                    'status' => 'pendente',
                ]);

                EntregarWebhook::dispatch($entrega->id);
            });
    }
}
