<?php

namespace App\Services\Ia;

/**
 * Interface desacoplada de provider (ver plano §10) - qual API/modelo usar
 * fica pro usuário decidir via .env (AI_PROVIDER/AI_API_KEY), o código não
 * trava numa escolha. AiProviderFactory resolve a implementação certa.
 */
interface AiProvider
{
    public function completar(string $prompt, array $contexto = []): string;

    public function disponivel(): bool;
}
