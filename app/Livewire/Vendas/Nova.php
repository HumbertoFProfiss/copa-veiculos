<?php

namespace App\Livewire\Vendas;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Venda;
use App\Services\Financeiro\ComissaoCalculator;
use App\Services\Vendas\ConfirmadorVenda;
use Livewire\Component;

class Nova extends Component
{
    public ?int $veiculo_id = null;

    public ?int $cliente_id = null;

    public ?int $vendedor_id = null;

    public ?float $preco_venda = null;

    public float $desconto = 0;

    public string $data_venda = '';

    public int $prazo_garantia_dias = 90;

    /** "pendente" pra vendas que ainda dependem de aprovação/financiamento; "confirmada" fecha a venda na hora (reserva o veículo, gera repasse etc). */
    public string $status = 'pendente';

    /**
     * Formas de pagamento da venda - uma venda pode ser paga combinando
     * varias (parte dinheiro, parte financiado, carro na troca...). Cada
     * linha vira um registro em venda_pagamentos; a soma precisa bater com
     * o valor liquido da venda (preco_venda - desconto).
     */
    public array $pagamentos = [
        ['forma' => 'dinheiro', 'valor' => null, 'observacao' => null],
    ];

    public ?string $troca_marca = null;

    public ?string $troca_modelo = null;

    public ?int $troca_ano_modelo = null;

    public ?string $troca_placa = null;

    public ?int $troca_km = null;

    public ?float $troca_valor_avaliado = null;

    /** Lucro que a loja ganha com a comissao do banco/financeira ou da operadora de cartao nessa venda. */
    public float $lucro_financeiro = 0;

    public ?string $instituicao_financeira = null;

    public float $impostos = 0;

    /**
     * Preco anunciado do veiculo selecionado - guardado a parte do
     * preco_venda (que aqui vira o valor final efetivamente vendido) pra
     * poder calcular o desconto automaticamente quando o vendedor digita
     * um valor de venda menor que o anunciado.
     */
    public ?float $precoAnunciado = null;

    public array $formasPagamentoDisponiveis = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'transferencia' => 'Transferência',
        'cartao_debito' => 'Cartão de débito',
        'cartao_credito' => 'Cartão de crédito',
        'financiamento' => 'Financiamento',
        'consorcio' => 'Consórcio',
        'troca' => 'Veículo na troca',
    ];

    public function mount(): void
    {
        $this->data_venda = now()->format('Y-m-d');
        $this->vendedor_id = auth()->id();
    }

    public function updatedVeiculoId($valor): void
    {
        $this->precoAnunciado = Veiculo::find($valor)?->preco_venda;
        $this->preco_venda = $this->precoAnunciado;
        $this->desconto = 0;
        $this->sincronizarPagamentoUnico();
    }

    public function updatedPrecoVenda($valor): void
    {
        if ($this->precoAnunciado !== null) {
            $this->desconto = max(0, round($this->precoAnunciado - (float) $valor, 2));
        }

        $this->sincronizarPagamentoUnico();
    }

    public function updatedDesconto(): void
    {
        $this->sincronizarPagamentoUnico();
    }

    /**
     * Caso comum: a venda inteira e paga numa unica forma de pagamento -
     * preenche o valor dessa linha sozinho a partir do valor da venda, pra
     * nao obrigar o vendedor a digitar o mesmo numero duas vezes. So entra
     * em acao enquanto houver exatamente uma linha (ver adicionarPagamento).
     */
    protected function sincronizarPagamentoUnico(): void
    {
        if (count($this->pagamentos) === 1) {
            $this->pagamentos[0]['valor'] = $this->totalVenda();
        }
    }

    public function adicionarPagamento(): void
    {
        $this->pagamentos[] = ['forma' => 'dinheiro', 'valor' => null, 'observacao' => null];
    }

    public function removerPagamento(int $indice): void
    {
        if (count($this->pagamentos) <= 1) {
            return;
        }

        unset($this->pagamentos[$indice]);
        $this->pagamentos = array_values($this->pagamentos);
        $this->sincronizarPagamentoUnico();
    }

    public function totalPagamentos(): float
    {
        return round(collect($this->pagamentos)->sum(fn ($p) => (float) ($p['valor'] ?? 0)), 2);
    }

    public function totalVenda(): float
    {
        return round((float) $this->preco_venda - (float) $this->desconto, 2);
    }

    public function temPagamentoTroca(): bool
    {
        return collect($this->pagamentos)->contains(fn ($p) => ($p['forma'] ?? null) === 'troca');
    }

    protected function rules(): array
    {
        $trocaObrigatoria = $this->temPagamentoTroca() ? 'required' : 'nullable';

        return [
            'veiculo_id' => 'required|exists:veiculos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'preco_venda' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'data_venda' => 'required|date',
            'prazo_garantia_dias' => 'required|integer|min:0',
            'status' => 'required|in:pendente,confirmada',
            'pagamentos' => 'required|array|min:1',
            'pagamentos.*.forma' => 'required|in:'.implode(',', array_keys($this->formasPagamentoDisponiveis)),
            'pagamentos.*.valor' => 'required|numeric|min:0.01',
            'pagamentos.*.observacao' => 'nullable|string|max:255',
            'troca_marca' => "{$trocaObrigatoria}|string|max:255",
            'troca_modelo' => "{$trocaObrigatoria}|string|max:255",
            'troca_ano_modelo' => 'nullable|integer|min:1950|max:'.(now()->year + 1),
            'troca_placa' => 'nullable|string|max:8',
            'troca_km' => 'nullable|integer|min:0',
            'troca_valor_avaliado' => "{$trocaObrigatoria}|numeric|min:0",
            'lucro_financeiro' => 'nullable|numeric|min:0',
            'instituicao_financeira' => 'nullable|string|max:100',
            'impostos' => 'nullable|numeric|min:0',
        ];
    }

    public function salvar()
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate();

        if (abs($this->totalPagamentos() - $this->totalVenda()) > 0.01) {
            $this->addError('pagamentos', sprintf(
                'A soma das formas de pagamento (R$ %s) precisa bater com o valor da venda (R$ %s).',
                number_format($this->totalPagamentos(), 2, ',', '.'),
                number_format($this->totalVenda(), 2, ',', '.'),
            ));

            return;
        }

        $dadosTroca = [
            'marca' => $dados['troca_marca'] ?? null,
            'modelo' => $dados['troca_modelo'] ?? null,
            'ano_modelo' => $dados['troca_ano_modelo'] ?? null,
            'placa' => $dados['troca_placa'] ?? null,
            'km' => $dados['troca_km'] ?? null,
            'valor_avaliado' => $dados['troca_valor_avaliado'] ?? null,
        ];
        unset($dados['troca_marca'], $dados['troca_modelo'], $dados['troca_ano_modelo'], $dados['troca_placa'], $dados['troca_km'], $dados['troca_valor_avaliado']);

        $pagamentos = $dados['pagamentos'];
        unset($dados['pagamentos']);

        $dados['forma_pagamento'] = count($pagamentos) > 1
            ? 'misto'
            : $pagamentos[0]['forma'];

        $dados['filial_id'] = Veiculo::find($dados['veiculo_id'])?->filial_id;

        $confirmarAgora = $dados['status'] === 'confirmada';
        $dados['status'] = 'pendente';

        $venda = Venda::create($dados);

        foreach ($pagamentos as $pagamento) {
            $venda->pagamentos()->create([
                'forma_pagamento' => $pagamento['forma'],
                'valor' => $pagamento['valor'],
                'observacao' => $pagamento['observacao'] ?? null,
            ]);
        }

        if ($this->temPagamentoTroca()) {
            $venda->carroTroca()->create($dadosTroca);
        }

        $venda->update(['comissao_vendedor' => (new ComissaoCalculator)->calcular($venda)]);

        if ($confirmarAgora) {
            (new ConfirmadorVenda)->confirmar($venda);
        }

        session()->flash('sucesso', $confirmarAgora ? 'Venda registrada e confirmada com sucesso.' : 'Venda registrada como pendente.');

        return redirect()->route('admin.vendas.index');
    }

    public function render()
    {
        return view('livewire.vendas.nova', [
            'veiculos' => Veiculo::where('status', 'disponivel')->orderBy('marca')->get(),
            'clientes' => Cliente::orderBy('nome')->get(),
            'vendedores' => User::orderBy('name')->get(),
        ])->layout('layouts.admin', ['title' => 'Nova venda']);
    }
}
