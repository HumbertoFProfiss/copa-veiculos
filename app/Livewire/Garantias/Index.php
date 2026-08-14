<?php

namespace App\Livewire\Garantias;

use App\Livewire\Concerns\WithDataTable;
use App\Models\GarantiaChamado;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    #[Url(as: 'status')]
    public string $filtroStatus = '';

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
        ])->layout('layouts.admin', ['title' => 'Garantias']);
    }
}
