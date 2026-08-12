<?php

namespace App\Livewire\Vendas;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Venda;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    protected function query(): Builder
    {
        return Venda::with(['veiculo', 'cliente', 'vendedor']);
    }

    public function render()
    {
        return view('livewire.vendas.index', [
            'vendas' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Vendas']);
    }
}
