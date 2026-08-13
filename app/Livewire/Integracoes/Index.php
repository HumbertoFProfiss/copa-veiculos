<?php

namespace App\Livewire\Integracoes;

use App\Jobs\EntregarWebhook;
use App\Models\Webhook;
use App\Models\WebhookEntrega;
use Livewire\Component;

class Index extends Component
{
    public string $novoTokenNome = '';

    public ?string $tokenGerado = null;

    public string $webhookUrl = '';

    public array $webhookEventos = [];

    public ?int $editandoWebhookId = null;

    public function mount(): void
    {
        $this->authorize('integracoes.ver');
    }

    public function criarToken(): void
    {
        $this->authorize('integracoes.criar');

        $this->validate(['novoTokenNome' => 'required|string|max:100']);

        $token = auth()->user()->createToken($this->novoTokenNome);

        $this->tokenGerado = $token->plainTextToken;
        $this->novoTokenNome = '';
    }

    public function fecharTokenGerado(): void
    {
        $this->tokenGerado = null;
    }

    public function revogarToken(int $id): void
    {
        $this->authorize('integracoes.excluir');

        auth()->user()->tokens()->where('id', $id)->delete();
        session()->flash('sucesso', 'Token revogado.');
    }

    public function editarWebhook(?int $id = null): void
    {
        $this->authorize($id ? 'integracoes.editar' : 'integracoes.criar');

        if ($id) {
            $webhook = Webhook::findOrFail($id);
            $this->editandoWebhookId = $id;
            $this->webhookUrl = $webhook->url;
            $this->webhookEventos = $webhook->eventos ?? [];
        } else {
            $this->editandoWebhookId = null;
            $this->webhookUrl = '';
            $this->webhookEventos = [];
        }
    }

    public function salvarWebhook(): void
    {
        $this->authorize($this->editandoWebhookId ? 'integracoes.editar' : 'integracoes.criar');

        $this->validate([
            'webhookUrl' => 'required|url|max:255',
            'webhookEventos' => 'required|array|min:1',
        ]);

        if ($this->editandoWebhookId) {
            Webhook::findOrFail($this->editandoWebhookId)->update([
                'url' => $this->webhookUrl,
                'eventos' => $this->webhookEventos,
            ]);
        } else {
            Webhook::create([
                'url' => $this->webhookUrl,
                'eventos' => $this->webhookEventos,
                'ativo' => true,
            ]);
        }

        $this->reset(['webhookUrl', 'webhookEventos', 'editandoWebhookId']);
        session()->flash('sucesso', 'Webhook salvo.');
    }

    public function alternarAtivo(int $id): void
    {
        $this->authorize('integracoes.editar');

        $webhook = Webhook::findOrFail($id);
        $webhook->update(['ativo' => ! $webhook->ativo]);
    }

    public function regenerarSecret(int $id): void
    {
        $this->authorize('integracoes.editar');

        Webhook::findOrFail($id)->update(['secret' => \Illuminate\Support\Str::random(40)]);
        session()->flash('sucesso', 'Novo secret gerado.');
    }

    public function excluirWebhook(int $id): void
    {
        $this->authorize('integracoes.excluir');

        Webhook::findOrFail($id)->delete();
        session()->flash('sucesso', 'Webhook removido.');
    }

    public function reenviarEntrega(int $id): void
    {
        $this->authorize('integracoes.editar');

        $entrega = WebhookEntrega::findOrFail($id);
        $entrega->update(['status' => 'pendente']);
        EntregarWebhook::dispatch($entrega->id);
        session()->flash('sucesso', 'Reenvio agendado.');
    }

    public function render()
    {
        return view('livewire.integracoes.index', [
            'tokens' => auth()->user()->tokens()->latest()->get(),
            'webhooks' => Webhook::with(['entregas' => fn ($q) => $q->limit(5)])->latest()->get(),
            'eventosDisponiveis' => Webhook::EVENTOS_DISPONIVEIS,
        ])->layout('layouts.admin', ['title' => 'Integrações']);
    }
}
