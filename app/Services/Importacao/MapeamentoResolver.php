<?php

namespace App\Services\Importacao;

use App\Models\MapeamentoImportacao;
use Illuminate\Support\Str;

/**
 * Sugere o de-para inicial (coluna do arquivo => campo destino) antes do
 * ajuste manual na tela de preview: primeiro tenta um mapeamento já salvo
 * da mesma origem (empresa já importou desse cliente/formato antes), depois
 * o mapeamentoSugerido() do adapter, depois fuzzy match por nome de coluna.
 */
class MapeamentoResolver
{
    public function sugerir(ImportadorAdapter $adapter, array $colunasDoArquivo): array
    {
        $salvo = MapeamentoImportacao::where('origem', $adapter->origem())->latest()->first();

        if ($salvo) {
            return array_intersect_key($salvo->mapeamento, array_flip($colunasDoArquivo));
        }

        $sugestaoAdapter = $adapter->mapeamentoSugerido();
        $mapeamento = [];

        foreach ($colunasDoArquivo as $coluna) {
            if (isset($sugestaoAdapter[$coluna])) {
                $mapeamento[$coluna] = $sugestaoAdapter[$coluna];

                continue;
            }

            $campoEncontrado = $this->fuzzyMatch($coluna, $adapter->camposDestino());

            if ($campoEncontrado) {
                $mapeamento[$coluna] = $campoEncontrado;
            }
        }

        return $mapeamento;
    }

    protected function fuzzyMatch(string $coluna, array $camposDestino): ?string
    {
        $colunaNormalizada = Str::of($coluna)->lower()->ascii()->replace([' ', '_', '-', '.'], '');

        foreach ($camposDestino as $campo => $label) {
            $campoNormalizado = Str::of($campo)->lower()->replace('_', '');
            $labelNormalizado = Str::of($label)->lower()->ascii()->replace([' ', '_', '-', '.'], '');

            if ($colunaNormalizada->exactly($campoNormalizado) || $colunaNormalizada->exactly($labelNormalizado)) {
                return $campo;
            }
        }

        return null;
    }

    public function salvar(string $origem, string $nome, array $mapeamento): MapeamentoImportacao
    {
        return MapeamentoImportacao::create([
            'nome' => $nome,
            'origem' => $origem,
            'mapeamento' => $mapeamento,
        ]);
    }
}
