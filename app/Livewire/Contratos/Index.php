<?php

namespace App\Livewire\Contratos;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Contrato;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    protected function query(): Builder
    {
        return Contrato::with(['modelo', 'cliente', 'veiculo']);
    }

    public function render()
    {
        return view('livewire.contratos.index', [
            'contratos' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Contratos']);
    }
}
