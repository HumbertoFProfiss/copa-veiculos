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

    public string $forma_pagamento = 'avista';

    public ?float $preco_venda = null;

    public float $desconto = 0;

    public string $data_venda = '';

    public int $prazo_garantia_dias = 90;

    /** "pendente" pra vendas que ainda dependem de aprovação/financiamento; "confirmada" fecha a venda na hora (reserva o veículo, gera repasse etc). */
    public string $status = 'pendente';

    public ?string $troca_marca = null;

    public ?string $troca_modelo = null;

    public ?int $troca_ano_modelo = null;

    public ?string $troca_placa = null;

    public ?int $troca_km = null;

    public ?float $troca_valor_avaliado = null;

    public ?float $valor_entrada = null;

    public ?float $valor_financiado = null;

    /**
     * Preco anunciado do veiculo selecionado - guardado a parte do
     * preco_venda (que aqui vira o valor final efetivamente vendido) pra
     * poder calcular o desconto automaticamente quando o vendedor digita
     * um valor de venda menor que o anunciado.
     */
    public ?float $precoAnunciado = null;

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
        $this->valor_entrada = null;
        $this->valor_financiado = null;
    }

    public function updatedPrecoVenda($valor): void
    {
        if ($this->precoAnunciado === null) {
            return;
        }

        $this->desconto = max(0, round($this->precoAnunciado - (float) $valor, 2));
    }

    /**
     * Separa, pra venda financiada, quanto e pago direto (entrada) de
     * quanto vai pro banco (financiado, o que gera comissao do banco) -
     * preenchendo um dos dois calcula o outro a partir do valor total.
     */
    public function updatedValorEntrada($valor): void
    {
        $total = (float) $this->preco_venda - (float) $this->desconto;
        $this->valor_financiado = max(0, round($total - (float) $valor, 2));
    }

    public function updatedValorFinanciado($valor): void
    {
        $total = (float) $this->preco_venda - (float) $this->desconto;
        $this->valor_entrada = max(0, round($total - (float) $valor, 2));
    }

    protected function rules(): array
    {
        $trocaObrigatoria = $this->forma_pagamento === 'troca' ? 'required' : 'nullable';
        $financiadoObrigatorio = $this->forma_pagamento === 'financiado' ? 'required' : 'nullable';

        return [
            'veiculo_id' => 'required|exists:veiculos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'forma_pagamento' => 'required|in:avista,financiado,consorcio,troca',
            'preco_venda' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'data_venda' => 'required|date',
            'prazo_garantia_dias' => 'required|integer|min:0',
            'status' => 'required|in:pendente,confirmada',
            'troca_marca' => "{$trocaObrigatoria}|string|max:255",
            'troca_modelo' => "{$trocaObrigatoria}|string|max:255",
            'troca_ano_modelo' => 'nullable|integer|min:1950|max:'.(now()->year + 1),
            'troca_placa' => 'nullable|string|max:8',
            'troca_km' => 'nullable|integer|min:0',
            'troca_valor_avaliado' => "{$trocaObrigatoria}|numeric|min:0",
            'valor_entrada' => "{$financiadoObrigatorio}|numeric|min:0",
            'valor_financiado' => "{$financiadoObrigatorio}|numeric|min:0",
        ];
    }

    public function salvar()
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate();

        $dadosTroca = [
            'marca' => $dados['troca_marca'],
            'modelo' => $dados['troca_modelo'],
            'ano_modelo' => $dados['troca_ano_modelo'],
            'placa' => $dados['troca_placa'],
            'km' => $dados['troca_km'],
            'valor_avaliado' => $dados['troca_valor_avaliado'],
        ];
        unset($dados['troca_marca'], $dados['troca_modelo'], $dados['troca_ano_modelo'], $dados['troca_placa'], $dados['troca_km'], $dados['troca_valor_avaliado']);

        if ($dados['forma_pagamento'] !== 'financiado') {
            $dados['valor_entrada'] = null;
            $dados['valor_financiado'] = null;
        }

        $dados['filial_id'] = Veiculo::find($dados['veiculo_id'])?->filial_id;

        $confirmarAgora = $dados['status'] === 'confirmada';
        $dados['status'] = 'pendente';

        $venda = Venda::create($dados);

        if ($venda->forma_pagamento === 'troca') {
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
