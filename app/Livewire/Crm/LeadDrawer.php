<?php

namespace App\Livewire\Crm;

use App\Models\Lead;
use App\Models\LeadHistorico;
use App\Models\LeadTarefa;
use Livewire\Component;

class LeadDrawer extends Component
{
    public int $leadId;

    public string $novaTarefaTitulo = '';

    public string $novaTarefaVencimento = '';

    public string $novaAnotacao = '';

    public function mount(int $leadId): void
    {
        $this->leadId = $leadId;
    }

    protected function lead(): Lead
    {
        return Lead::with(['veiculo', 'vendedor', 'historico.usuario', 'tarefas' => fn ($q) => $q->orderBy('concluida')->orderBy('vencimento_em')])
            ->findOrFail($this->leadId);
    }

    public function fechar(): void
    {
        $this->dispatch('lead-atualizado');
    }

    public function moverEstagio(string $novoEstagio): void
    {
        $this->authorize('leads.editar');

        if (! array_key_exists($novoEstagio, Lead::ESTAGIO_LABELS)) {
            return;
        }

        $lead = Lead::findOrFail($this->leadId);
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

        $this->dispatch('pipeline-atualizado');
    }

    public function registrarContato(): void
    {
        $this->authorize('leads.editar');

        LeadHistorico::create([
            'lead_id' => $this->leadId,
            'user_id' => auth()->id(),
            'acao' => 'contato_registrado',
            'detalhes' => $this->novaAnotacao ?: 'Contato registrado.',
        ]);

        $this->novaAnotacao = '';
    }

    public function adicionarTarefa(): void
    {
        $this->authorize('leads.editar');
        $this->validate([
            'novaTarefaTitulo' => 'required|string|max:150',
            'novaTarefaVencimento' => 'required|date',
        ]);

        LeadTarefa::create([
            'lead_id' => $this->leadId,
            'user_id' => auth()->id(),
            'titulo' => $this->novaTarefaTitulo,
            'vencimento_em' => $this->novaTarefaVencimento,
        ]);

        $this->reset(['novaTarefaTitulo', 'novaTarefaVencimento']);
    }

    public function concluirTarefa(int $tarefaId): void
    {
        LeadTarefa::where('id', $tarefaId)->update([
            'concluida' => true,
            'concluida_em' => now(),
        ]);
    }

    public function render()
    {
        return view('livewire.crm.lead-drawer', [
            'lead' => $this->lead(),
        ]);
    }
}
