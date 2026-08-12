<?php

namespace App\Livewire\Vendas;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Venda;
use App\Services\Financeiro\ComissaoCalculator;
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

    public function mount(): void
    {
        $this->data_venda = now()->format('Y-m-d');
        $this->vendedor_id = auth()->id();
    }

    public function updatedVeiculoId($valor): void
    {
        $this->preco_venda = Veiculo::find($valor)?->preco_venda;
    }

    protected function rules(): array
    {
        return [
            'veiculo_id' => 'required|exists:veiculos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'forma_pagamento' => 'required|in:avista,financiado,consorcio,troca',
            'preco_venda' => 'required|numeric|min:0',
            'desconto' => 'nullable|numeric|min:0',
            'data_venda' => 'required|date',
            'prazo_garantia_dias' => 'required|integer|min:0',
        ];
    }

    public function salvar()
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate();
        $dados['status'] = 'confirmada';

        $venda = Venda::create($dados);

        $venda->update(['comissao_vendedor' => (new ComissaoCalculator)->calcular($venda)]);

        $venda->veiculo->update(['status' => 'vendido']);

        // Auto-despublicação em todos os canais onde o veículo estava
        // publicado (ver plano §7/§9) - dispara 1 job por canal publicado.
        \App\Models\Publicacao::where('veiculo_id', $venda->veiculo_id)
            ->where('status', 'publicado')
            ->get()
            ->each(fn ($publicacao) => \App\Jobs\DespublicarVeiculoEmCanal::dispatch(
                $venda->empresa_id,
                $publicacao->id,
            ));

        session()->flash('sucesso', 'Venda registrada com sucesso.');

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
