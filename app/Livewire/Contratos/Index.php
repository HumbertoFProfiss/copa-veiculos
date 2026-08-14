<?php

namespace App\Livewire\Contratos;

use App\Livewire\Concerns\WithDataTable;
use App\Models\Contrato;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Index extends Component
{
    use WithDataTable;

    public function enviarParaAssinatura(int $id): void
    {
        $this->authorize('contratos.criar');

        $contrato = Contrato::findOrFail($id);
        if ($contrato->status !== 'rascunho') {
            return;
        }

        $contrato->update([
            'status' => 'enviado',
            'assinatura_provider' => 'simulado',
            'assinatura_status' => 'pendente',
            'assinatura_metadata' => ['enviado_em' => now()->toIso8601String(), 'simulacao' => true],
        ]);
    }

    public function marcarAssinado(int $id): void
    {
        $this->authorize('contratos.criar');

        $contrato = Contrato::findOrFail($id);
        if ($contrato->assinatura_status !== 'pendente') {
            return;
        }

        $contrato->update([
            'status' => 'assinado',
            'assinatura_status' => 'assinado',
            'assinatura_metadata' => array_merge($contrato->assinatura_metadata ?? [], ['assinado_em' => now()->toIso8601String()]),
        ]);
    }

    public function recusarAssinatura(int $id): void
    {
        $this->authorize('contratos.criar');

        $contrato = Contrato::findOrFail($id);
        if ($contrato->assinatura_status !== 'pendente') {
            return;
        }

        $contrato->update([
            'assinatura_status' => 'recusado',
            'assinatura_metadata' => array_merge($contrato->assinatura_metadata ?? [], ['recusado_em' => now()->toIso8601String()]),
        ]);
    }

    protected function query(): Builder
    {
        return Contrato::with(['modelo', 'cliente', 'veiculo'])
            ->when($this->busca, function (Builder $q) {
                $termo = "%{$this->busca}%";
                $q->where(function (Builder $q) use ($termo) {
                    $q->where('numero', 'like', $termo)
                        ->orWhereHas('cliente', fn (Builder $q) => $q->where('nome', 'like', $termo))
                        ->orWhereHas('veiculo', function (Builder $q) use ($termo) {
                            $q->where('marca', 'like', $termo)->orWhere('modelo', 'like', $termo);
                        });
                });
            });
    }

    public function render()
    {
        return view('livewire.contratos.index', [
            'contratos' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
        ])->layout('layouts.admin', ['title' => 'Contratos']);
    }
}
