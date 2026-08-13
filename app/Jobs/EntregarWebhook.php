<?php

namespace App\Jobs;

use App\Models\WebhookEntrega;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

/**
 * Entrega uma WebhookEntrega ja registrada (ver WebhookDispatcher) via POST
 * assinado (HMAC-SHA256 do corpo, com o secret do proprio Webhook) - mesmo
 * padrao usado por Stripe/GitHub pro destinatario validar autenticidade.
 */
class EntregarWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [30, 120, 600, 1800, 3600];

    public function __construct(public int $webhookEntregaId) {}

    public function handle(): void
    {
        $entrega = WebhookEntrega::with('webhook')->find($this->webhookEntregaId);

        if (! $entrega || ! $entrega->webhook) {
            return;
        }

        $webhook = $entrega->webhook;
        $corpo = json_encode($entrega->payload);
        $assinatura = hash_hmac('sha256', $corpo, $webhook->secret);

        $entrega->increment('tentativas');

        try {
            $resposta = Http::timeout(10)
                ->withBody($corpo, 'application/json')
                ->withHeaders([
                    'X-Copa-Event' => $entrega->evento,
                    'X-Copa-Signature' => 'sha256='.$assinatura,
                ])
                ->post($webhook->url);

            $entrega->update([
                'status' => $resposta->successful() ? 'sucesso' : 'falhou',
                'resposta_http' => $resposta->status(),
                'resposta_corpo' => mb_substr($resposta->body(), 0, 2000),
                'enviado_em' => now(),
            ]);

            // Nao-2xx: joga excecao pra deixar o mecanismo normal de retry/backoff
            // do job agir (tries=5) - status ja fica 'falhou' registrado acima
            // enquanto isso, e so nao muda mais se a proxima tentativa nao passar
            // por aqui de novo com sucesso.
            if (! $resposta->successful()) {
                throw new \RuntimeException("Webhook respondeu HTTP {$resposta->status()}");
            }
        } catch (\Throwable $e) {
            $entrega->update([
                'status' => 'falhou',
                'resposta_corpo' => mb_substr($e->getMessage(), 0, 2000),
                'enviado_em' => now(),
            ]);

            throw $e;
        }
    }
}
