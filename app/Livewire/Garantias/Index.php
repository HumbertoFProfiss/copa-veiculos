<?php

namespace App\Livewire\Garantias;

use App\Livewire\Concerns\WithDataTable;
use App\Models\GarantiaChamado;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    #[Url(as: 'status')]
    public string $filtroStatus = '';

    public bool $mostrarFormNova = false;

    public ?int $venda_id = null;

    public string $descricao_problema = '';

    public string $novoStatus = 'aberto';

    public ?float $custo_peca = null;

    public ?float $custo_servico = null;

    protected function rules(): array
    {
        return [
            'venda_id' => 'required|exists:vendas,id',
            'descricao_problema' => 'required|string|max:255',
            'novoStatus' => 'required|in:aberto,em_analise,aprovado,recusado,concluido',
            'custo_peca' => 'nullable|numeric|min:0',
            'custo_servico' => 'nullable|numeric|min:0',
        ];
    }

    public function novaGarantia(): void
    {
        $this->authorize('vendas.criar');
        $this->reset(['venda_id', 'descricao_problema', 'custo_peca', 'custo_servico']);
        $this->novoStatus = 'aberto';
        $this->mostrarFormNova = true;
    }

    public function salvarGarantia(): void
    {
        $this->authorize('vendas.criar');

        $dados = $this->validate();
        $venda = Venda::findOrFail($dados['venda_id']);

        $venda->garantiasChamados()->create([
            'descricao_problema' => $dados['descricao_problema'],
            'status' => $dados['novoStatus'],
            'custo_peca' => $dados['custo_peca'],
            'custo_servico' => $dados['custo_servico'],
            'veiculo_id' => $venda->veiculo_id,
            'cliente_id' => $venda->cliente_id,
        ]);

        $this->mostrarFormNova = false;
        session()->flash('sucesso', 'Chamado de garantia criado.');
    }

    protected function query(): Builder
    {
        return GarantiaChamado::with(['venda', 'veiculo', 'cliente'])
            ->when($this->busca, function (Builder $q) {
                $termo = "%{$this->busca}%";
                $q->where(function (Builder $q) use ($termo) {
                    $q->where('descricao_problema', 'like', $termo)
                        ->orWhereHas('cliente', fn (Builder $q) => $q->where('nome', 'like', $termo))
                        ->orWhereHas('veiculo', function (Builder $q) use ($termo) {
                            $q->where('marca', 'like', $termo)->orWhere('modelo', 'like', $termo);
                        });
                });
            })
            ->when($this->filtroStatus, fn (Builder $q) => $q->where('status', $this->filtroStatus));
    }

    public function render()
    {
        $chamados = $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina);

        return view('livewire.garantias.index', [
            'chamados' => $chamados,
            'statusLabels' => GarantiaChamado::STATUS_LABELS,
            'totalCustos' => (clone $this->query())->get()->sum(fn (GarantiaChamado $g) => (float) $g->custo_peca + (float) $g->custo_servico),
            'vendas' => Venda::with(['veiculo', 'cliente'])->orderByDesc('data_venda')->get(),
        ])->layout('layouts.admin', ['title' => 'Garantias']);
    }
}
