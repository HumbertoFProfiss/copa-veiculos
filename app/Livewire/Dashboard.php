<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Models\Lead;
use App\Models\Venda;
use App\Models\Veiculo;
use Illuminate\Support\Collection;
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

    /** Quantos meses (incluindo o atual) os gráficos do dashboard mostram. */
    public const MESES_GRAFICO = 6;

    protected function seriesUltimosMeses(): array
    {
        $labels = [];
        $vendasQtd = [];
        $vendasReceita = [];
        $leadsQtd = [];

        for ($i = self::MESES_GRAFICO - 1; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $inicio = $mes->copy()->startOfMonth();
            $fim = $mes->copy()->endOfMonth();

            $labels[] = ucfirst($mes->translatedFormat('M/y'));

            $vendasDoMes = Venda::where('status', 'confirmada')->whereBetween('data_venda', [$inicio, $fim])->get();
            $vendasQtd[] = $vendasDoMes->count();
            $vendasReceita[] = round((float) $vendasDoMes->sum(fn (Venda $v) => (float) $v->preco_venda - (float) $v->desconto), 2);

            $leadsQtd[] = Lead::whereBetween('created_at', [$inicio, $fim])->where('lead_falso', false)->count();
        }

        return compact('labels', 'vendasQtd', 'vendasReceita', 'leadsQtd');
    }

    /**
     * Feed de atividades recentes (últimos 10 eventos), combinando veículo
     * cadastrado, cliente cadastrado, venda confirmada e lead recebido -
     * cada modelo já tem created_at, só precisa mesclar e ordenar.
     */
    protected function atividadesRecentes(): Collection
    {
        $veiculos = Veiculo::latest()->take(8)->get()->map(fn (Veiculo $v) => [
            'icone' => 'truck',
            'cor' => 'text-primary bg-primary-soft',
            'texto' => "Veículo cadastrado: {$v->marca} {$v->modelo}",
            'data' => $v->created_at,
            'url' => route('admin.veiculos.editar', $v),
        ]);

        $clientes = Cliente::latest()->take(8)->get()->map(fn (Cliente $c) => [
            'icone' => 'user',
            'cor' => 'text-primary bg-primary-soft',
            'texto' => "Cliente cadastrado: {$c->nome}",
            'data' => $c->created_at,
            'url' => route('admin.clientes.index'),
        ]);

        $vendas = Venda::where('status', 'confirmada')->with(['veiculo', 'cliente'])->latest()->take(8)->get()
            ->map(fn (Venda $v) => [
                'icone' => 'banknotes',
                'cor' => 'text-success bg-success/10',
                'texto' => "Venda registrada: {$v->veiculo?->marca} {$v->veiculo?->modelo} para {$v->cliente?->nome}",
                'data' => $v->created_at,
                'url' => route('admin.vendas.index'),
            ]);

        $leads = Lead::where('lead_falso', false)->latest()->take(8)->get()->map(fn (Lead $l) => [
            'icone' => 'chat-bubble-left-right',
            'cor' => 'text-warning bg-warning/10',
            'texto' => "Novo lead: {$l->nome}",
            'data' => $l->created_at,
            'url' => route('admin.leads.inbox'),
        ]);

        return $veiculos->concat($clientes)->concat($vendas)->concat($leads)
            ->sortByDesc('data')
            ->take(10)
            ->values();
    }

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
            'series' => $this->seriesUltimosMeses(),
            'atividades' => $this->atividadesRecentes(),
        ])->layout('layouts.admin', ['title' => 'Dashboard']);
    }
}
