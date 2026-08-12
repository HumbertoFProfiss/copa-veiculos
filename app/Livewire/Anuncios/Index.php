<?php

namespace App\Livewire\Anuncios;

use App\Jobs\PublicarVeiculoEmCanal;
use App\Livewire\Concerns\WithDataTable;
use App\Models\Publicacao;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

/**
 * Painel de status de publicação por veículo x canal (ver plano §7) - a
 * publicação em si acontece na tela do veículo (PublicacaoMatrix); aqui é
 * a visão consolidada de tudo, com reprocessar em lote pros que erraram.
 */
class Index extends Component
{
    use WithDataTable;

    public string $filtroStatus = '';

    public function reprocessar(int $publicacaoId): void
    {
        $this->authorize('anuncios.criar');
        $publicacao = Publicacao::findOrFail($publicacaoId);
        PublicarVeiculoEmCanal::dispatchSync($publicacao->empresa_id, $publicacao->veiculo_id, $publicacao->canal_id);
    }

    protected function query(): Builder
    {
        return Publicacao::with(['veiculo', 'canal'])
            ->when($this->filtroStatus, fn (Builder $q) => $q->where('status', $this->filtroStatus));
    }

    public function render()
    {
        return view('livewire.anuncios.index', [
            'publicacoes' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Anúncios']);
    }
}
