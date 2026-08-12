<?php

namespace App\Services\Ia;

use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP real pro formato /chat/completions estilo OpenAI - compatível
 * com vários provedores econômicos/gratuitos que expõem essa mesma forma de
 * API (ver plano: "modelo de IA barato/gratuito"). A URL base e o modelo
 * ficam configuráveis via .env pra não travar numa escolha de provider.
 */
class OpenAiCompatibleProvider implements AiProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = 'https://api.openai.com/v1',
        protected string $modelo = 'gpt-4o-mini',
    ) {}

    public function disponivel(): bool
    {
        return filled($this->apiKey);
    }

    public function completar(string $prompt, array $contexto = []): string
    {
        if (! $this->disponivel()) {
            return (new NullAiProvider)->completar($prompt, $contexto);
        }

        $resposta = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->modelo,
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente de uma revenda de veículos no Brasil. Responda de forma objetiva e comercial, em português.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.4,
            ]);

        if ($resposta->failed()) {
            return 'Erro ao consultar a IA: '.($resposta->json('error.message') ?? $resposta->status());
        }

        return trim((string) $resposta->json('choices.0.message.content', ''));
    }
}
