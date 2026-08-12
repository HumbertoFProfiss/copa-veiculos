<?php

namespace App\Livewire\Financeiro;

use App\Livewire\Concerns\WithDataTable;
use App\Models\CategoriaFinanceira;
use App\Models\ContaPagar;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ContasPagar extends Component
{
    use WithDataTable;

    public bool $mostrarForm = false;

    public ?int $editandoId = null;

    public ?int $categoria_id = null;

    public string $descricao = '';

    public float $valor = 0;

    public string $vencimento = '';

    public string $recorrencia = 'nenhuma';

    protected function rules(): array
    {
        return [
            'descricao' => 'required|string|max:150',
            'valor' => 'required|numeric|min:0.01',
            'vencimento' => 'required|date',
            'categoria_id' => 'nullable|exists:categorias_financeiras,id',
            'recorrencia' => 'required|in:nenhuma,semanal,mensal,anual',
        ];
    }

    public function novo(): void
    {
        $this->authorize('financeiro.criar');
        $this->reset(['editandoId', 'categoria_id', 'descricao', 'valor', 'vencimento']);
        $this->recorrencia = 'nenhuma';
        $this->mostrarForm = true;
    }

    public function salvar(): void
    {
        $this->authorize('financeiro.criar');
        $dados = $this->validate();
        $dados['status'] = 'pendente';

        ContaPagar::create($dados);

        $this->mostrarForm = false;
        session()->flash('sucesso', 'Conta a pagar lançada.');
    }

    public function marcarPago(int $id): void
    {
        $this->authorize('financeiro.aprovar');
        ContaPagar::where('id', $id)->update(['status' => 'pago', 'pagamento' => now()]);
    }

    protected function query(): Builder
    {
        return ContaPagar::with(['categoria', 'fornecedor']);
    }

    public function render()
    {
        return view('livewire.financeiro.contas-pagar', [
            'contas' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'categorias' => CategoriaFinanceira::where('tipo', 'despesa')->get(),
            'totalPendente' => ContaPagar::where('status', 'pendente')->sum('valor'),
        ])->layout('layouts.admin', ['title' => 'Contas a Pagar']);
    }
}
