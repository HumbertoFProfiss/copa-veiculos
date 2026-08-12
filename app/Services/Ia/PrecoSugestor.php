<?php

namespace App\Services\Ia;

use App\Models\IaSugestao;
use App\Models\Veiculo;

/**
 * Cruza FIPE + dias em pátio + custos acumulados + margem alvo + preços de
 * anúncio do mesmo modelo/ano/KM já cadastrados internamente (não faz
 * scraping de portal - dado que já temos) e devolve faixa sugerida (ver
 * plano §10). Sempre grava como sugestão pendente; nunca altera
 * veiculos.preco_venda sozinho.
 */
class PrecoSugestor
{
    public function __construct(protected AiProvider $provider) {}

    public function sugerir(Veiculo $veiculo): IaSugestao
    {
        $similares = Veiculo::where('marca', $veiculo->marca)
            ->where('modelo', $veiculo->modelo)
            ->where('id', '!=', $veiculo->id)
            ->whereNotNull('preco_venda')
            ->limit(5)
            ->get(['preco_venda', 'km', 'ano_modelo']);

        $contexto = [
            'marca' => $veiculo->marca,
            'modelo' => $veiculo->modelo,
            'ano' => "{$veiculo->ano_fabricacao}/{$veiculo->ano_modelo}",
            'km' => $veiculo->km,
            'preco_fipe' => (float) $veiculo->preco_tabela_fipe,
            'preco_compra' => (float) $veiculo->preco_compra,
            'custos_acumulados' => (float) $veiculo->custos()->sum('valor'),
            'dias_em_patio' => $veiculo->diasEmPatio(),
            'preco_atual' => (float) $veiculo->preco_venda,
            'similares_cadastrados' => $similares->toArray(),
        ];

        $prompt = "Sugira uma faixa de preço de venda (mínimo e ideal) para este veículo, "
            ."explicando o raciocínio em até 3 frases. Dados: ".json_encode($contexto, JSON_UNESCAPED_UNICODE);

        $resposta = $this->provider->completar($prompt, $contexto);

        return IaSugestao::create([
            'tipo' => 'preco',
            'sugerivel_type' => Veiculo::class,
            'sugerivel_id' => $veiculo->id,
            'conteudo_sugerido' => $resposta,
            'contexto_usado' => $contexto,
            'status' => 'pendente',
        ]);
    }
}
