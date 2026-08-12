<?php

namespace App\Services\Ia;

/**
 * Fallback quando AI_PROVIDER não está configurado (ver .env) - nunca quebra
 * a tela, só avisa claramente que a IA não está ligada ainda, com instrução
 * de como configurar. Isso é o estado padrão até o usuário obter uma API key
 * (ver plano §Contexto - módulo de menor prioridade, sem custo de terceiro
 * até ser configurado).
 */
class NullAiProvider implements AiProvider
{
    public function completar(string $prompt, array $contexto = []): string
    {
        return 'Assistente de IA não configurado. Defina AI_PROVIDER e AI_API_KEY no .env para ativar.';
    }

    public function disponivel(): bool
    {
        return false;
    }
}
