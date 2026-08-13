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
        $contexto = [
            'marca' => $veiculo->marca,
            'modelo' => $veiculo->modelo,
            'versao' => $veiculo->versao,
            'ano' => "{$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}",
            'km' => $veiculo->km,
            'cor' => $veiculo->cor,
            'opcionais' => $veiculo->opcionais->pluck('nome')->implode(', '),
        ];

        $resposta = $this->gerarTexto($contexto, $limiteCaracteres);

        return IaSugestao::create([
            'tipo' => 'descricao',
            'sugerivel_type' => Veiculo::class,
            'sugerivel_id' => $veiculo->id,
            'conteudo_sugerido' => $resposta,
            'contexto_usado' => $contexto + ['limite_caracteres' => $limiteCaracteres],
            'status' => 'pendente',
        ]);
    }

    /**
     * Mesma geração, mas sem exigir um Veiculo já salvo - usada na tela de
     * Novo Veículo, onde ainda não existe um ID pra vincular a IaSugestao.
     * Aqui a sugestão não fica persistida: o formulário guarda o texto
     * temporariamente até o usuário aceitar (preencher a descrição) ou
     * descartar.
     *
     * @param  array{marca?: ?string, modelo?: ?string, versao?: ?string, ano?: ?string, km?: int, cor?: ?string, opcionais?: string}  $dados
     */
    public function gerarTexto(array $dados, int $limiteCaracteres = 500): string
    {
        $contexto = $dados + ['limite_caracteres' => $limiteCaracteres];

        $prompt = "Escreva um título curto e uma descrição comercial (máximo {$limiteCaracteres} caracteres) "
            ."pra anunciar este veículo num portal de venda de carros. Tom vendedor, direto, sem emojis. "
            .'Dados: '.json_encode($contexto, JSON_UNESCAPED_UNICODE);

        $resposta = $this->provider->completar($prompt, $contexto);

        if (strlen($resposta) > $limiteCaracteres) {
            $resposta = substr($resposta, 0, $limiteCaracteres - 1).'…';
        }

        return $resposta;
    }
}
