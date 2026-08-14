<?php

namespace App\Livewire\Vendas;

use App\Models\Banco;
use App\Models\GarantiaChamado;
use App\Models\NotaFiscal;
use App\Models\PropostaFinanciamento;
use App\Models\RenaveTransferencia;
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

    public bool $mostrarFormFinanciamento = false;

    public ?int $financiamento_banco_id = null;

    public ?float $financiamento_valor_financiado = null;

    public float $financiamento_entrada = 0;

    public int $financiamento_num_parcelas = 48;

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

    public function abrirFormFinanciamento(): void
    {
        $this->authorize('vendas.criar');
        $this->reset(['financiamento_banco_id', 'financiamento_valor_financiado']);
        $this->financiamento_entrada = 0;
        $this->financiamento_num_parcelas = 48;
        $this->mostrarFormFinanciamento = true;
    }

    public function simularFinanciamento(): void
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate([
            'financiamento_banco_id' => 'required|exists:bancos,id',
            'financiamento_valor_financiado' => 'required|numeric|min:1',
            'financiamento_entrada' => 'nullable|numeric|min:0',
            'financiamento_num_parcelas' => 'required|integer|min:1|max:60',
        ]);

        $banco = Banco::findOrFail($dados['financiamento_banco_id']);

        $this->venda->propostasFinanciamento()->create([
            'banco_id' => $banco->id,
            'criado_por' => auth()->id(),
            'valor_financiado' => $dados['financiamento_valor_financiado'],
            'entrada' => $dados['financiamento_entrada'] ?? 0,
            'num_parcelas' => $dados['financiamento_num_parcelas'],
            'taxa_juros_am' => $banco->taxa_juros_am_padrao,
            'valor_parcela' => PropostaFinanciamento::calcularParcela(
                (float) $dados['financiamento_valor_financiado'],
                (float) $banco->taxa_juros_am_padrao,
                (int) $dados['financiamento_num_parcelas'],
            ),
            'status' => 'simulada',
        ]);

        $this->mostrarFormFinanciamento = false;
        $this->venda->refresh();
        session()->flash('sucesso', 'Proposta de financiamento simulada.');
    }

    public function aprovarProposta(int $id): void
    {
        $this->authorize('vendas.criar');
        PropostaFinanciamento::findOrFail($id)->update(['status' => 'aprovada']);
        $this->venda->refresh();
    }

    public function recusarProposta(int $id): void
    {
        $this->authorize('vendas.criar');
        PropostaFinanciamento::findOrFail($id)->update(['status' => 'recusada']);
        $this->venda->refresh();
    }

    public function excluirProposta(int $id): void
    {
        $this->authorize('vendas.criar');
        PropostaFinanciamento::findOrFail($id)->delete();
        $this->venda->refresh();
    }

    public function emitirNotaFiscal(): void
    {
        $this->authorize('vendas.criar');

        if ($this->venda->status !== 'confirmada') {
            session()->flash('erro', 'Só é possível emitir nota fiscal de uma venda confirmada.');

            return;
        }

        if ($this->venda->notasFiscais()->where('status', 'emitida')->exists()) {
            session()->flash('erro', 'Já existe uma nota fiscal emitida pra essa venda.');

            return;
        }

        $this->venda->notasFiscais()->create([
            'emitida_por' => auth()->id(),
            'numero' => str_pad((string) (NotaFiscal::count() + 1), 6, '0', STR_PAD_LEFT),
            'serie' => '1',
            'chave_acesso' => NotaFiscal::gerarChaveAcessoSimulada(),
            'valor' => (float) $this->venda->preco_venda - (float) $this->venda->desconto,
            'status' => 'emitida',
            'emitida_em' => now(),
        ]);

        $this->venda->refresh();
        session()->flash('sucesso', 'Nota fiscal simulada emitida.');
    }

    public function cancelarNotaFiscal(int $id): void
    {
        $this->authorize('vendas.criar');
        NotaFiscal::findOrFail($id)->update(['status' => 'cancelada', 'cancelada_em' => now()]);
        $this->venda->refresh();
        session()->flash('sucesso', 'Nota fiscal simulada cancelada.');
    }

    public function gerarTransferenciaRenave(): void
    {
        $this->authorize('vendas.criar');

        if ($this->venda->status !== 'confirmada') {
            session()->flash('erro', 'Só é possível registrar a transferência Renave de uma venda confirmada.');

            return;
        }

        if ($this->venda->renaveTransferencias()->where('status', 'concluida')->exists()) {
            session()->flash('erro', 'Já existe uma transferência Renave concluída pra essa venda.');

            return;
        }

        $this->venda->renaveTransferencias()->create([
            'gerada_por' => auth()->id(),
            'protocolo' => RenaveTransferencia::gerarProtocoloSimulado(),
            'status' => 'concluida',
            'transferida_em' => now(),
        ]);

        $this->venda->refresh();
        session()->flash('sucesso', 'Transferência Renave simulada registrada.');
    }

    public function cancelarTransferenciaRenave(int $id): void
    {
        $this->authorize('vendas.criar');
        RenaveTransferencia::findOrFail($id)->update(['status' => 'cancelada', 'cancelada_em' => now()]);
        $this->venda->refresh();
        session()->flash('sucesso', 'Transferência Renave simulada cancelada.');
    }

    public function render()
    {
        $this->venda->load(['veiculo', 'cliente', 'vendedor', 'filial', 'parcelas', 'carroTroca', 'garantiasChamados', 'propostasFinanciamento.banco', 'notasFiscais', 'renaveTransferencias']);

        return view('livewire.vendas.show', [
            'statusLabels' => GarantiaChamado::STATUS_LABELS,
            'totalCustosGarantia' => $this->venda->garantiasChamados->sum(fn (GarantiaChamado $g) => (float) $g->custo_peca + (float) $g->custo_servico),
            'bancos' => Banco::where('ativo', true)->orderBy('nome')->get(),
        ])->layout('layouts.admin', ['title' => 'Venda #'.$this->venda->id]);
    }
}
