<?php

namespace App\Livewire\Relatorios;

use App\Models\Veiculo;
use Livewire\Component;

/**
 * Relatório de estoque: idade (dias em pátio), giro implícito, valor
 * imobilizado total e curva ABC (classificação de Pareto pelo valor de cada
 * veículo - A = veículos que juntos somam até 80% do valor em estoque, B até
 * 95%, C o resto) - ver prompt seção 4.13.
 */
class Estoque extends Component
{
    public function classificarAbc()
    {
        $veiculos = Veiculo::where('status', 'disponivel')
            ->orderByDesc('preco_venda')
            ->get();

        $valorTotal = (float) $veiculos->sum('preco_venda');
        $acumulado = 0.0;

        return $veiculos->map(function (Veiculo $v) use (&$acumulado, $valorTotal) {
            $acumulado += (float) $v->preco_venda;
            $percentualAcumulado = $valorTotal > 0 ? ($acumulado / $valorTotal) * 100 : 0;

            $classe = match (true) {
                $percentualAcumulado <= 80 => 'A',
                $percentualAcumulado <= 95 => 'B',
                default => 'C',
            };

            return ['veiculo' => $v, 'classe' => $classe, 'percentualAcumulado' => round($percentualAcumulado, 1)];
        });
    }

    public function exportar()
    {
        $this->authorize('relatorios.ver');

        return (new \App\Exports\EstoqueExport)->download('estoque-'.now()->format('Y-m-d').'.xlsx');
    }

    public function render()
    {
        $classificados = $this->classificarAbc();

        return view('livewire.relatorios.estoque', [
            'classificados' => $classificados,
            'valorImobilizado' => $classificados->sum(fn ($c) => (float) $c['veiculo']->preco_venda),
            'contagemPorClasse' => $classificados->groupBy('classe')->map->count(),
        ])->layout('layouts.admin', ['title' => 'Relatório de Estoque']);
    }
}
