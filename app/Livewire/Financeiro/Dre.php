<?php

namespace App\Livewire\Financeiro;

use App\Models\ContaPagar;
use App\Models\CustoVeiculo;
use App\Models\Venda;
use Livewire\Component;

class Dre extends Component
{
    public string $mes;

    public function mount(): void
    {
        $this->mes = now()->format('Y-m');
    }

    protected function periodo(): array
    {
        $inicio = \Carbon\Carbon::createFromFormat('Y-m', $this->mes)->startOfMonth();
        $fim = $inicio->copy()->endOfMonth();

        return [$inicio, $fim];
    }

    public function render()
    {
        [$inicio, $fim] = $this->periodo();

        $vendas = Venda::where('status', 'confirmada')->whereBetween('data_venda', [$inicio, $fim])->get();

        $receitaBruta = $vendas->sum(fn (Venda $v) => (float) $v->preco_venda - (float) $v->desconto);
        $custoVeiculos = $vendas->sum(fn (Venda $v) => (float) ($v->veiculo->preco_compra ?? 0));
        $custosAgregados = CustoVeiculo::whereIn('veiculo_id', $vendas->pluck('veiculo_id'))->sum('valor');
        $comissoes = $vendas->sum('comissao_vendedor');

        $despesasOperacionais = ContaPagar::where('status', 'pago')
            ->whereBetween('pagamento', [$inicio, $fim])
            ->sum('valor');

        $lucroLiquido = $receitaBruta - $custoVeiculos - $custosAgregados - $comissoes - $despesasOperacionais;

        return view('livewire.financeiro.dre', [
            'totalVendas' => $vendas->count(),
            'receitaBruta' => $receitaBruta,
            'custoVeiculos' => $custoVeiculos,
            'custosAgregados' => $custosAgregados,
            'comissoes' => $comissoes,
            'despesasOperacionais' => $despesasOperacionais,
            'lucroLiquido' => $lucroLiquido,
        ])->layout('layouts.admin', ['title' => 'DRE']);
    }
}
