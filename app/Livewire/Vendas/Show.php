<?php

namespace App\Livewire\Vendas;

use App\Models\GarantiaChamado;
use App\Models\Venda;
use App\Services\Vendas\ConfirmadorVenda;
use Livewire\Component;

class Show extends Component
{
    public Venda $venda;

    public bool $mostrarFormGarantia = false;

    public ?int $editandoGarantiaId = null;

    public string $descricao_problema = '';

    public string $status = 'aberto';

    public ?float $custo_peca = null;

    public ?float $custo_servico = null;

    protected function rules(): array
    {
        return [
            'descricao_problema' => 'required|string|max:255',
            'status' => 'required|in:aberto,em_analise,aprovado,recusado,concluido',
            'custo_peca' => 'nullable|numeric|min:0',
            'custo_servico' => 'nullable|numeric|min:0',
        ];
    }

    public function confirmarVenda(): void
    {
        $this->authorize('vendas.criar');

        if ($this->venda->status !== 'pendente') {
            return;
        }

        (new ConfirmadorVenda)->confirmar($this->venda);
        $this->venda->refresh();
        session()->flash('sucesso', 'Venda confirmada.');
    }

    public function cancelarVenda(): void
    {
        $this->authorize('vendas.criar');

        if ($this->venda->status === 'cancelada') {
            return;
        }

        (new ConfirmadorVenda)->cancelar($this->venda);
        $this->venda->refresh();
        session()->flash('sucesso', 'Venda cancelada.');
    }

    public function novaGarantia(): void
    {
        $this->authorize('vendas.criar');
        $this->reset(['editandoGarantiaId', 'descricao_problema', 'custo_peca', 'custo_servico']);
        $this->status = 'aberto';
        $this->mostrarFormGarantia = true;
    }

    public function editarGarantia(int $id): void
    {
        $this->authorize('vendas.criar');
        $garantia = GarantiaChamado::findOrFail($id);
        $this->editandoGarantiaId = $garantia->id;
        $this->fill($garantia->only(['descricao_problema', 'status', 'custo_peca', 'custo_servico']));
        $this->mostrarFormGarantia = true;
    }

    public function salvarGarantia(): void
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate();

        if ($this->editandoGarantiaId) {
            GarantiaChamado::findOrFail($this->editandoGarantiaId)->update($dados);
        } else {
            $this->venda->garantiasChamados()->create($dados + [
                'veiculo_id' => $this->venda->veiculo_id,
                'cliente_id' => $this->venda->cliente_id,
            ]);
        }

        $this->mostrarFormGarantia = false;
        $this->venda->refresh();
        session()->flash('sucesso', 'Chamado de garantia salvo.');
    }

    public function excluirGarantia(int $id): void
    {
        $this->authorize('vendas.criar');
        GarantiaChamado::findOrFail($id)->delete();
        $this->venda->refresh();
        session()->flash('sucesso', 'Chamado de garantia removido.');
    }

    public function render()
    {
        $this->venda->load(['veiculo', 'cliente', 'vendedor', 'filial', 'parcelas', 'carroTroca', 'garantiasChamados']);

        return view('livewire.vendas.show', [
            'statusLabels' => GarantiaChamado::STATUS_LABELS,
            'totalCustosGarantia' => $this->venda->garantiasChamados->sum(fn (GarantiaChamado $g) => (float) $g->custo_peca + (float) $g->custo_servico),
        ])->layout('layouts.admin', ['title' => 'Venda #'.$this->venda->id]);
    }
}
