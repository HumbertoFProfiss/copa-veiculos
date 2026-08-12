<?php

namespace App\Services\Importacao;

class CsvGenericoImportador implements ImportadorAdapter
{
    public function origem(): string
    {
        return 'csv_generico';
    }

    public function camposDestino(): array
    {
        return [
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'versao' => 'Versão',
            'ano_fabricacao' => 'Ano de fabricação',
            'ano_modelo' => 'Ano do modelo',
            'km' => 'Quilometragem',
            'combustivel' => 'Combustível',
            'cambio' => 'Câmbio',
            'cor' => 'Cor',
            'placa' => 'Placa',
            'numero_chassi' => 'Chassi',
            'preco_venda' => 'Preço',
        ];
    }

    public function mapeamentoSugerido(): array
    {
        // Sem export de referência - o próprio nome da coluna do arquivo do
        // usuário é sugerido como destino quando bate (case-insensitive) com
        // um dos campos acima; o resto fica pro ajuste manual na tela de preview.
        return [];
    }

    public function parseLinha(array $linha, array $mapeamento): VeiculoImportado
    {
        return VeiculoImportadorHelper::aplicarMapeamento($linha, $mapeamento);
    }
}
