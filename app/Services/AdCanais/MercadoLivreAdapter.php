<?php

namespace App\Services\AdCanais;

use App\Models\Publicacao;
use App\Models\Veiculo;
use App\Services\AdCanais\Exceptions\AdCanalException;

/**
 * Stub - pronto pra virar integração real assim que existir credencial
 * OAuth em canal_credenciais (Mercado Livre exige app registrado + fluxo
 * OAuth, não é self-serve). estaConfigurado() reflete isso: false até a
 * credencial existir.
 */
class MercadoLivreAdapter implements AdCanalAdapter
{
    public function slug(): string
    {
        return 'mercadolivre';
    }

    public function estaConfigurado(): bool
    {
        return false;
    }

    public function montarPayload(Veiculo $veiculo): array
    {
        return [
            'title' => trim("{$veiculo->marca} {$veiculo->modelo} {$veiculo->versao}"),
            'price' => (float) $veiculo->preco_venda,
        ];
    }

    public function publicar(Veiculo $veiculo, ?Publicacao $existente): AdCanalResultado
    {
        throw new AdCanalException('Integração Mercado Livre requer OAuth - configure em Configurações > Canais.');
    }

    public function despublicar(Veiculo $veiculo, Publicacao $publicacao): AdCanalResultado
    {
        throw new AdCanalException('Integração Mercado Livre requer OAuth - configure em Configurações > Canais.');
    }
}
