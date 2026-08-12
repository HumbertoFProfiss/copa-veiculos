<?php

namespace App\Livewire\Crm;

use App\Models\Lead;
use App\Models\LeadHistorico;
use Livewire\Attributes\On;
use Livewire\Component;

class Pipeline extends Component
{
    public ?int $leadSelecionadoId = null;

    public function moverEstagio(int $leadId, string $novoEstagio): void
    {
        $this->authorize('leads.editar');

        if (! array_key_exists($novoEstagio, Lead::ESTAGIO_LABELS)) {
            return;
        }

        $lead = Lead::findOrFail($leadId);
        $estagioAnterior = $lead->estagio;

        if ($estagioAnterior === $novoEstagio) {
            return;
        }

        $lead->update(['estagio' => $novoEstagio]);

        LeadHistorico::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'acao' => 'mudanca_estagio',
            'detalhes' => Lead::ESTAGIO_LABELS[$estagioAnterior].' → '.Lead::ESTAGIO_LABELS[$novoEstagio],
        ]);
    }

    public function abrirLead(int $leadId): void
    {
        $this->leadSelecionadoId = $leadId;
    }

    #[On('lead-atualizado')]
    public function fecharDrawer(): void
    {
        $this->leadSelecionadoId = null;
    }

    #[On('pipeline-atualizado')]
    public function atualizar(): void
    {
        // método vazio de propósito: a mera presença do listener já faz o
        // Livewire re-renderizar esse componente (e re-rodar a query em
        // render()) quando o LeadDrawer despachar o evento após mover o
        // lead de estágio.
    }

    public function render()
    {
        $leadsPorEstagio = Lead::with('veiculo')
            ->whereNotIn('estagio', ['ganho', 'perdido'])
            ->orWhere(function ($q) {
                $q->whereIn('estagio', ['ganho', 'perdido'])
                    ->where('updated_at', '>=', now()->subDays(30));
            })
            ->latest()
            ->get()
            ->groupBy('estagio');

        return view('livewire.crm.pipeline', [
            'colunas' => Lead::ESTAGIO_LABELS,
            'leadsPorEstagio' => $leadsPorEstagio,
        ])->layout('layouts.admin', ['title' => 'CRM']);
    }
}
