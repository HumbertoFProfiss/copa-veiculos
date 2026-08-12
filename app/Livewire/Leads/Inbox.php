<?php

namespace App\Livewire\Leads;

use App\Livewire\Concerns\WithDataTable;
use App\Models\BloqueioLead;
use App\Models\Lead;
use App\Services\Leads\LeadDeduplicator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class Inbox extends Component
{
    use WithDataTable;

    public string $filtroPortal = '';

    public bool $mostrarFalsos = false;

    public function marcarFalso(int $leadId, ?string $motivo = null): void
    {
        $this->authorize('leads.editar');

        $lead = Lead::findOrFail($leadId);
        $lead->update([
            'lead_falso' => true,
            'motivo_bloqueio' => $motivo ?: 'Marcado manualmente como falso',
        ]);

        if ($lead->telefone) {
            $telefoneNormalizado = (new LeadDeduplicator)->normalizarTelefone($lead->telefone);
            BloqueioLead::firstOrCreate(
                ['tipo' => 'telefone', 'valor' => $telefoneNormalizado],
                ['motivo' => 'Marcado como falso a partir do lead #'.$lead->id]
            );
        }
    }

    public function reverterFalso(int $leadId): void
    {
        $this->authorize('leads.editar');
        Lead::where('id', $leadId)->update(['lead_falso' => false, 'motivo_bloqueio' => null]);
    }

    protected function query(): Builder
    {
        return Lead::query()
            ->with('contato')
            ->when(! $this->mostrarFalsos, fn (Builder $q) => $q->where('lead_falso', false))
            ->when($this->filtroPortal, fn (Builder $q) => $q->where('portal_origem', $this->filtroPortal))
            ->when($this->busca, function (Builder $q) {
                $termo = "%{$this->busca}%";
                $q->where(fn (Builder $q) => $q->where('nome', 'like', $termo)->orWhere('telefone', 'like', $termo));
            });
    }

    protected function relatorioQualidadePorPortal(): array
    {
        return Lead::query()
            ->whereNotNull('portal_origem')
            ->selectRaw('portal_origem, count(*) as total, sum(lead_falso) as falsos')
            ->groupBy('portal_origem')
            ->get()
            ->map(fn ($linha) => [
                'portal' => $linha->portal_origem,
                'total' => $linha->total,
                'falsos' => $linha->falsos,
                'validos' => $linha->total - $linha->falsos,
                'percentualValido' => $linha->total > 0 ? round((($linha->total - $linha->falsos) / $linha->total) * 100) : 0,
            ])
            ->all();
    }

    public function render()
    {
        return view('livewire.leads.inbox', [
            'leads' => $this->query()->orderBy($this->ordenarPor, $this->ordenarDirecao)->paginate($this->porPagina),
            'relatorioQualidade' => $this->relatorioQualidadePorPortal(),
        ])->layout('layouts.admin', ['title' => 'Caixa de Leads']);
    }
}
