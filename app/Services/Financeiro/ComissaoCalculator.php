<?php

namespace App\Services\Financeiro;

use App\Models\Venda;

/**
 * Comissão por percentual sobre o LUCRO da venda (preço de venda - desconto
 * - custo de compra - custos agregados do veículo), usando o percentual
 * cadastrado no vendedor (users.comissao_percentual). Ver plano §Financeiro -
 * regra "percentual sobre lucro" é a mais comum entre revendas; valor fixo/
 * escalonado ficam como extensão futura sobre a mesma base de cálculo.
 */
class ComissaoCalculator
{
    public function calcular(Venda $venda): float
    {
        $percentual = (float) ($venda->vendedor->comissao_percentual ?? 0);

        if ($percentual <= 0) {
            return 0.0;
        }

        $lucro = $this->lucroLiquido($venda);

        return $lucro > 0 ? round($lucro * ($percentual / 100), 2) : 0.0;
    }

    public function lucroLiquido(Venda $venda): float
    {
        $veiculo = $venda->veiculo;
        $custoCompra = (float) ($veiculo->preco_compra ?? 0);
        $custosAgregados = (float) $veiculo->custos()->sum('valor');
        $valorLiquidoVenda = (float) $venda->preco_venda - (float) $venda->desconto;

        return $valorLiquidoVenda - $custoCompra - $custosAgregados;
    }
}
