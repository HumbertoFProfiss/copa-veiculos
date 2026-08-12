<?php

namespace App\Services\AdCanais;

use App\Models\Publicacao;
use App\Models\Veiculo;

/**
 * Real - o "canal" é o próprio site (ver rota veiculo.show). Publicar aqui
 * só precisa que o veículo esteja com status=disponivel (a visibilidade no
 * site já depende disso, ver EstoqueController/VeiculoController) - não tem
 * feed externo, o "anúncio" É a página do veículo.
 */
class SiteProprioAdapter implements AdCanalAdapter
{
    public function slug(): string
    {
        return 'site_proprio';
    }

    public function estaConfigurado(): bool
    {
        return true;
    }

    public function montarPayload(Veiculo $veiculo): array
    {
        return [
            'id' => $veiculo->id,
            'url' => route('veiculo.show', $veiculo),
            'titulo' => "{$veiculo->marca} {$veiculo->modelo} {$veiculo->versao}",
            'preco' => (float) $veiculo->preco_venda,
        ];
    }

    public function publicar(Veiculo $veiculo, ?Publicacao $existente): AdCanalResultado
    {
        $payload = $this->montarPayload($veiculo);

        return AdCanalResultado::publicado(
            externalId: (string) $veiculo->id,
            urlAnuncio: $payload['url'],
            payload: $payload,
        );
    }

    public function despublicar(Veiculo $veiculo, Publicacao $publicacao): AdCanalResultado
    {
        return AdCanalResultado::despublicado();
    }
}
