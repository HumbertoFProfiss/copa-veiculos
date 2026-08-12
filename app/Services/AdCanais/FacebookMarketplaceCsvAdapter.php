<?php

namespace App\Services\AdCanais;

use App\Models\Publicacao;
use App\Models\Veiculo;

/**
 * Real - campos do feed de veículos documentado publicamente pela Meta
 * (Commerce Manager > catálogo automotivo). ATENÇÃO: conferir o schema exato
 * no Commerce Manager antes de um upload real de produção - a Meta revisa
 * esses campos periodicamente e isso aqui é a melhor referência pública
 * disponível no momento em que foi escrito, não uma integração via API
 * (que exigiria OAuth/Business Manager - fora do escopo desta fase).
 */
class FacebookMarketplaceCsvAdapter implements AdCanalAdapter
{
    public function slug(): string
    {
        return 'facebook';
    }

    public function estaConfigurado(): bool
    {
        return true;
    }

    public function montarPayload(Veiculo $veiculo): array
    {
        return [
            'vehicle_id' => (string) $veiculo->id,
            'title' => trim("{$veiculo->marca} {$veiculo->modelo} {$veiculo->versao}"),
            'description' => "Veículo {$veiculo->marca} {$veiculo->modelo}, ano {$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}, {$veiculo->km} km.",
            'url' => route('veiculo.show', $veiculo),
            'make' => $veiculo->marca,
            'model' => $veiculo->modelo,
            'year' => (string) $veiculo->ano_modelo,
            'mileage.value' => (string) $veiculo->km,
            'mileage.unit' => 'KM',
            'price' => number_format((float) $veiculo->preco_venda, 2, '.', '').' BRL',
            'exterior_color' => $veiculo->cor,
            'transmission' => $veiculo->cambio,
            'body_style' => 'SEDAN',
            'fuel_type' => $veiculo->combustivel,
            'condition' => 'USED',
            'vin' => $veiculo->numero_chassi,
            'image_url' => optional($veiculo->fotos->first())->url(),
            'availability' => 'in stock',
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
