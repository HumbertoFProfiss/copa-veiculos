<?php

namespace App\Livewire\Veiculos;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Veiculo;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    #[Url(as: 'status')]
    public string $filtroStatus = '';

    public function excluir(int $id): void
    {
        $this->authorize('veiculos.excluir');

        $veiculo = Veiculo::findOrFail($id);
        $veiculo->delete();

        session()->flash('sucesso', 'Veículo removido.');
    }

    protected function query(): Builder
    {
        return Veiculo::query()
            ->when($this->busca, function (Builder $q) {
                $termo = "%{$this->busca}%";
                $q->where(function (Builder $q) use ($termo) {
                    $q->where('marca', 'like', $termo)
                        ->orWhere('modelo', 'like', $termo)
                        ->orWhere('placa', 'like', $termo)
                        ->orWhere('numero_chassi', 'like', $termo)
                        ->orWhere('numero_estoque', 'like', $termo);
                });
            })
            ->when($this->filtroStatus, fn (Builder $q) => $q->where('status', $this->filtroStatus));
    }

    public function render()
    {
        $veiculos = $this->query()
            ->with('fotos')
            ->orderBy($this->ordenarPor, $this->ordenarDirecao)
            ->paginate($this->porPagina);

        return view('livewire.veiculos.index', [
            'veiculos' => $veiculos,
            'statusOptions' => Veiculo::STATUS_LABELS,
        ])->layout('layouts.admin', ['title' => 'Estoque']);
    }
}
