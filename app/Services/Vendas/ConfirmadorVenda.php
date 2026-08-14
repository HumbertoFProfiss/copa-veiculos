<?php

namespace App\Services\Vendas;

use App\Events\VendaConfirmada;
use App\Jobs\DespublicarVeiculoEmCanal;
use App\Models\CategoriaFinanceira;
use App\Models\ContaPagar;
use App\Models\Publicacao;
use App\Models\Venda;

/**
 * Efeitos colaterais de confirmar uma venda (venda passa a existir de
 * verdade pro estoque/financeiro) - extraído pra ser reaproveitado tanto
 * quando a venda já nasce confirmada (Vendas\Nova) quanto quando uma venda
 * pendente é confirmada depois (Vendas\Show).
 */
class ConfirmadorVenda
{
    public function confirmar(Venda $venda): void
    {
        $venda->update(['status' => 'confirmada']);
        $venda->veiculo->update(['status' => 'vendido']);

        $this->gerarRepasseConsignado($venda);

        // Auto-despublicação em todos os canais onde o veículo estava
        // publicado (ver plano §7/§9) - dispara 1 job por canal publicado.
        Publicacao::where('veiculo_id', $venda->veiculo_id)
            ->where('status', 'publicado')
            ->get()
            ->each(fn ($publicacao) => DespublicarVeiculoEmCanal::dispatch(
                $venda->empresa_id,
                $publicacao->id,
            ));

        event(new VendaConfirmada($venda));
    }

    /**
     * Cancela a venda. Se ela já estava confirmada, devolve o veículo pro
     * estoque disponível - uma venda cancelada não pode continuar
     * "reservando" o carro.
     */
    public function cancelar(Venda $venda): void
    {
        $eraConfirmada = $venda->status === 'confirmada';

        $venda->update(['status' => 'cancelada']);

        if ($eraConfirmada && $venda->veiculo->status === 'vendido') {
            $venda->veiculo->update(['status' => 'disponivel']);
        }
    }

    protected function gerarRepasseConsignado(Venda $venda): void
    {
        $veiculo = $venda->veiculo;

        if ($veiculo->tipo_propriedade !== 'consignado' || blank($veiculo->consignado_nome)) {
            return;
        }

        $valorRepasse = $veiculo->repasseConsignado((float) $venda->preco_venda);

        if ($valorRepasse <= 0) {
            return;
        }

        $categoria = CategoriaFinanceira::firstOrCreate([
            'nome' => 'Repasse de consignação',
            'tipo' => 'despesa',
        ]);

        ContaPagar::create([
            'categoria_id' => $categoria->id,
            'descricao' => 'Repasse consignação - '.$veiculo->consignado_nome.' ('.$veiculo->marca.' '.$veiculo->modelo.')',
            'valor' => $valorRepasse,
            'vencimento' => $venda->data_venda,
            'status' => 'pendente',
        ]);
    }
}
