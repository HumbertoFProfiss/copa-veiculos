<?php

namespace App\Services\Importacao;

/**
 * Um adapter por formato/origem de export. GenericoImportador cobre CSV
 * arbitrário com mapeamento manual; BoomImportador vem com um de-para
 * pré-preenchido pros nomes de coluna mais comuns do Boom Sistemas (concorrente
 * citado nominalmente no prompt) - precisa ser conferido/ajustado contra um
 * export real na primeira migração de um cliente vindo de lá.
 */
interface ImportadorAdapter
{
    public function origem(): string;

    /** Campos do NOSSO cadastro (ver VeiculoImportado) que esse adapter sabe preencher. */
    public function camposDestino(): array;

    /**
     * De-para sugerido: nome de coluna do arquivo de origem => campo destino.
     * Usado pelo MapeamentoResolver como ponto de partida antes do ajuste manual.
     */
    public function mapeamentoSugerido(): array;

    public function parseLinha(array $linha, array $mapeamento): VeiculoImportado;
}
