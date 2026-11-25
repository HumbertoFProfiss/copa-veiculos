<?php

namespace App\Livewire\Financeiro;

use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Relatorio pedido pelo cliente: antes (sistema antigo) ela via, numa tela só,
 * o custo e o lucro de cada carro separado do cadastro do veículo. Hoje isso
 * fica dentro do formulário de cada veículo (aba "Custos"); esta tela junta
 * tudo de novo, carro a carro, pra dar aquela visão geral que ela tinha antes.
 */
class LucroVeiculos extends Component
{
    #[Url(as: 'status')]
    public string $filtroStatus = '';

    protected function query(): Builder
    {
        return Veiculo::query()
            ->with('custos')
            ->when($this->filtroStatus, fn (Builder $q) => $q->where('status', $this->filtroStatus));
    }

    public function render()
    {
        $veiculos = $this->query()->orderByDesc('data_entrada')->get()->map(function (Veiculo $v) {
            return [
                'veiculo' => $v,
                'custos' => (float) $v->custos->sum('valor'),
                'margem' => $v->margem(),
            ];
        });

        return view('livewire.financeiro.lucro-veiculos', [
            'linhas' => $veiculos,
            'statusOptions' => Veiculo::STATUS_LABELS,
            'totalCompra' => $veiculos->sum(fn ($l) => (float) $l['veiculo']->preco_compra),
            'totalCustos' => $veiculos->sum('custos'),
            'totalVenda' => $veiculos->sum(fn ($l) => (float) $l['veiculo']->preco_venda),
            'totalMargem' => $veiculos->sum('margem'),
        ])->layout('layouts.admin', ['title' => 'Lucro por Veículo']);
    }
}
