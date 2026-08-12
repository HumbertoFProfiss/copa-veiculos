<?php

namespace App\Livewire\Financeiro;

use App\Livewire\Concerns\WithDataTable;
use App\Models\CategoriaFinanceira;
use App\Models\ContaReceber;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ContasReceber extends Component
{
    use WithDataTable;

    public function marcarRecebido(int $id): void
    {
        $this->authorize('financeiro.aprovar');
        ContaReceber::where('id', $id)->update(['status' => 'recebido', 'pagamento' => now()]);
    }

    protected function query(): Builder
    {
        return ContaReceber::with(['categoria', 'cliente']);
    }

    public function render()
    {
        return view('livewire.financeiro.contas-receber', [
            'contas' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'totalPendente' => ContaReceber::where('status', 'pendente')->sum('valor'),
        ])->layout('layouts.admin', ['title' => 'Contas a Receber']);
    }
}
