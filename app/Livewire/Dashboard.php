<?php

namespace App\Livewire;

use App\Models\ContaPagar;
use App\Models\Lead;
use App\Models\Venda;
use App\Models\Veiculo;
use Livewire\Component;

/**
 * KPIs do dashboard inicial (ver plano §10/prompt 4.13): veículos
 * disponíveis, valor em estoque, dias médios em pátio, vendas do mês,
 * margem média, leads abertos, contas a vencer.
 */
class Dashboard extends Component
{
    /** Prazo maximo pro 1o atendimento antes de contar como "em atraso" (ver prompt §4.7 - SLA). */
    public const SLA_MINUTOS = 60;

    public function render()
    {
        $disponiveis = Veiculo::where('status', 'disponivel')->get();

        $vendasDoMes = Venda::where('status', 'confirmada')
            ->whereBetween('data_venda', [now()->startOfMonth(), now()->endOfMonth()])
            ->get();

        $margemMedia = $vendasDoMes->isNotEmpty()
            ? $vendasDoMes->avg(function (Venda $v) {
                $custo = (float) ($v->veiculo->preco_compra ?? 0);
                $liquido = (float) $v->preco_venda - (float) $v->desconto;

                return $custo > 0 ? (($liquido - $custo) / $custo) * 100 : 0;
            })
            : 0;

        return view('livewire.dashboard', [
            'totalDisponiveis' => $disponiveis->count(),
            'valorEmEstoque' => $disponiveis->sum('preco_venda'),
            'diasMedioPatio' => $disponiveis->isNotEmpty()
                ? round($disponiveis->avg(fn (Veiculo $v) => $v->diasEmPatio()))
                : 0,
            'vendasDoMes' => $vendasDoMes->count(),
            'margemMedia' => round($margemMedia, 1),
            'leadsAbertos' => Lead::whereNotIn('estagio', ['ganho', 'perdido'])->where('lead_falso', false)->count(),
            'leadsEmAtraso' => Lead::whereNotIn('estagio', ['ganho', 'perdido'])
                ->where('lead_falso', false)
                ->whereNull('respondido_em')
                ->where('created_at', '<=', now()->subMinutes(self::SLA_MINUTOS))
                ->count(),
            'contasAVencer' => ContaPagar::where('status', 'pendente')
                ->whereBetween('vencimento', [now(), now()->addDays(7)])
                ->sum('valor'),
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
