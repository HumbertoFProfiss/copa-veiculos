<?php

namespace App\Services\Ia;

use App\Models\IaSugestao;
use App\Models\Veiculo;

/**
 * Gera título/descrição de anúncio a partir da ficha do veículo (ver plano
 * §10). Respeita limite de caracteres por canal quando informado - cada
 * AdCanalAdapter pode ter seu próprio limite (WebMotors/iCarros costumam
 * limitar em ~500-1000 caracteres a descrição).
 */
class DescricaoGerador
{
    public function __construct(protected AiProvider $provider) {}

    public function gerar(Veiculo $veiculo, int $limiteCaracteres = 500): IaSugestao
    {
        $opcionais = $veiculo->opcionais->pluck('nome')->implode(', ');

        $contexto = [
            'marca' => $veiculo->marca,
            'modelo' => $veiculo->modelo,
            'versao' => $veiculo->versao,
            'ano' => "{$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}",
            'km' => $veiculo->km,
            'cor' => $veiculo->cor,
            'opcionais' => $opcionais,
            'limite_caracteres' => $limiteCaracteres,
        ];

        $prompt = "Escreva um título curto e uma descrição comercial (máximo {$limiteCaracteres} caracteres) "
            ."pra anunciar este veículo num portal de venda de carros. Tom vendedor, direto, sem emojis. "
            .'Dados: '.json_encode($contexto, JSON_UNESCAPED_UNICODE);

        $resposta = $this->provider->completar($prompt, $contexto);

        if (strlen($resposta) > $limiteCaracteres) {
            $resposta = substr($resposta, 0, $limiteCaracteres - 1).'…';
        }

        return IaSugestao::create([
            'tipo' => 'descricao',
            'sugerivel_type' => Veiculo::class,
            'sugerivel_id' => $veiculo->id,
            'conteudo_sugerido' => $resposta,
            'contexto_usado' => $contexto,
            'status' => 'pendente',
        ]);
    }
}
