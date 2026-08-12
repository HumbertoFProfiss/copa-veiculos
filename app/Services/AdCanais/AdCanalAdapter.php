<?php

namespace App\Services\AdCanais;

use App\Models\Publicacao;
use App\Models\Veiculo;

/**
 * Um adapter por canal (ver plano §7). estaConfigurado() volta false pros
 * canais que dependem de credencial/OAuth que ainda não existe (ex: Mercado
 * Livre) - publicar() nesse caso lança AdCanalException tratada pelo job,
 * caindo como status='erro' com mensagem clara, não falhando silenciosamente.
 */
interface AdCanalAdapter
{
    public function slug(): string;

    public function estaConfigurado(): bool;

    public function montarPayload(Veiculo $veiculo): array;

    public function publicar(Veiculo $veiculo, ?Publicacao $existente): AdCanalResultado;

    public function despublicar(Veiculo $veiculo, Publicacao $publicacao): AdCanalResultado;
}
