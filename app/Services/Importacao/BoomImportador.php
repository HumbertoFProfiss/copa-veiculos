<?php

namespace App\Services\Importacao;

/**
 * De-para pré-preenchido pro export de estoque do Boom Sistemas (concorrente
 * citado nominalmente no prompt do chefe). IMPORTANTE: os nomes de coluna
 * abaixo são uma melhor estimativa baseada em nomenclatura comum de sistemas
 * de gestão de revenda (Marca/Modelo/Versão/Ano Fab./Ano Mod./Km/Placa/Chassi/
 * Valor Venda) - nunca vimos um export real do Boom. Na primeira importação
 * de um cliente vindo de lá, conferir as colunas reais na tela de preview e
 * ajustar aqui (ou o usuário ajusta manualmente naquela importação e o
 * mapeamento fica salvo em mapeamentos_importacao pra reuso).
 */
class BoomImportador implements ImportadorAdapter
{
    public function origem(): string
    {
        return 'boom';
    }

    public function camposDestino(): array
    {
        return (new CsvGenericoImportador)->camposDestino();
    }

    public function mapeamentoSugerido(): array
    {
        return [
            'Marca' => 'marca',
            'Modelo' => 'modelo',
            'Versão' => 'versao',
            'Ano Fab.' => 'ano_fabricacao',
            'Ano Mod.' => 'ano_modelo',
            'Km' => 'km',
            'Combustível' => 'combustivel',
            'Câmbio' => 'cambio',
            'Cor' => 'cor',
            'Placa' => 'placa',
            'Chassi' => 'numero_chassi',
            'Valor Venda' => 'preco_venda',
        ];
    }

    public function parseLinha(array $linha, array $mapeamento): VeiculoImportado
    {
        return VeiculoImportadorHelper::aplicarMapeamento($linha, $mapeamento);
    }
}
