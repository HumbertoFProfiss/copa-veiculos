<?php

namespace App\Livewire\Financeiro;

use App\Models\ContaPagar;
use App\Models\CustoVeiculo;
use App\Models\GarantiaChamado;
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

    /**
     * Extraido do render() pra ficar testavel sem depender de propriedade
     * publica do componente Livewire (ver DreCustosGarantiaTest).
     */
    public function calcular(): array
    {
        [$inicio, $fim] = $this->periodo();

        $vendas = Venda::where('status', 'confirmada')->whereBetween('data_venda', [$inicio, $fim])->get();

        $receitaBruta = $vendas->sum(fn (Venda $v) => (float) $v->preco_venda - (float) $v->desconto);
        $custoVeiculos = $vendas->sum(fn (Venda $v) => (float) ($v->veiculo->preco_compra ?? 0));
        $custosAgregados = CustoVeiculo::whereIn('veiculo_id', $vendas->pluck('veiculo_id'))->sum('valor');
        $comissoes = $vendas->sum('comissao_vendedor');

        // Custos de garantia (chamados aprovados/concluidos) das vendas do
        // periodo - faltava entrar nessa conta, mesmo ja sendo descontado da
        // margem de cada veiculo individualmente (ver Veiculo::margem()) e
        // do card do Dashboard.
        $custosGarantia = GarantiaChamado::whereIn('status', ['aprovado', 'concluido'])
            ->whereIn('venda_id', $vendas->pluck('id'))
            ->get()
            ->sum(fn (GarantiaChamado $g) => (float) $g->custo_peca + (float) $g->custo_servico);

        $despesasOperacionais = ContaPagar::where('status', 'pago')
            ->whereBetween('pagamento', [$inicio, $fim])
            ->sum('valor');

        $lucroLiquido = $receitaBruta - $custoVeiculos - $custosAgregados - $custosGarantia - $comissoes - $despesasOperacionais;

        return [
            'totalVendas' => $vendas->count(),
            'receitaBruta' => $receitaBruta,
            'custoVeiculos' => $custoVeiculos,
            'custosAgregados' => $custosAgregados,
            'custosGarantia' => $custosGarantia,
            'comissoes' => $comissoes,
            'despesasOperacionais' => $despesasOperacionais,
            'lucroLiquido' => $lucroLiquido,
        ];
    }

    public function render()
    {
        return view('livewire.financeiro.dre', $this->calcular())
            ->layout('layouts.admin', ['title' => 'DRE']);
    }
}
