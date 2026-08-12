<?php

namespace App\Services\AdCanais;

use App\Models\Canal;
use App\Models\Publicacao;
use App\Models\Veiculo;

/**
 * Config-driven - cobre WebMotors/iCarros/Chaves na Mão/MobAutos/Na Pista/
 * Carros SP: é o CSV manual de hoje, só que melhorado (enfileirado, com
 * status rastreável e reprocessável) - real e testável, mas sem API/OAuth
 * de verdade (WebMotors exige credencial via portal de desenvolvedor
 * Sensedia + login Cockpit; iCarros passa por painel de revenda - nenhum
 * dos dois é self-serve sem contrato comercial, ver plano §7).
 */
class GenericPortalCsvAdapter implements AdCanalAdapter
{
    public function __construct(protected Canal $canal) {}

    public function slug(): string
    {
        return $this->canal->slug;
    }

    public function estaConfigurado(): bool
    {
        return true;
    }

    public function montarPayload(Veiculo $veiculo): array
    {
        return [
            'ID' => (string) $veiculo->id,
            'Titulo' => trim("{$veiculo->marca} {$veiculo->modelo} {$veiculo->versao}"),
            'Marca' => $veiculo->marca,
            'Modelo' => $veiculo->modelo,
            'AnoFabricacao' => (string) $veiculo->ano_fabricacao,
            'AnoModelo' => (string) $veiculo->ano_modelo,
            'Preco' => number_format((float) $veiculo->preco_venda, 2, '.', ''),
            'KM' => (string) $veiculo->km,
            'Combustivel' => $veiculo->combustivel,
            'Cambio' => $veiculo->cambio,
            'Cor' => $veiculo->cor,
            'Placa' => $veiculo->placa,
            'Chassi' => $veiculo->numero_chassi,
            'Opcionais' => $veiculo->opcionais->pluck('nome')->implode(', '),
            'URL' => route('veiculo.show', $veiculo),
            'FotoPrincipal' => optional($veiculo->fotos->first())->url(),
        ];
    }

    public function publicar(Veiculo $veiculo, ?Publicacao $existente): AdCanalResultado
    {
        $payload = $this->montarPayload($veiculo);

        return AdCanalResultado::publicado(
            externalId: (string) $veiculo->id,
            urlAnuncio: $payload['URL'],
            payload: $payload,
        );
    }

    public function despublicar(Veiculo $veiculo, Publicacao $publicacao): AdCanalResultado
    {
        return AdCanalResultado::despublicado();
    }
}
