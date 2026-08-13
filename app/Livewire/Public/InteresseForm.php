<?php

namespace App\Livewire\Public;

use App\Models\Lead;
use App\Models\Veiculo;
use App\Services\Leads\LeadDistribuidor;
use Livewire\Component;

/**
 * Formulário "tenho interesse" da página do veículo - cria lead instantaneamente
 * (ver prompt seção 4.6). Rate limit simples por sessão evita spam básico; a
 * detecção de lead falso de verdade (DDD, número descartável...) é o módulo
 * de Central de Leads Multicanal (ver plano §8), que roda por cima de todo
 * lead recebido, incluindo os capturados aqui.
 */
class InteresseForm extends Component
{
    public Veiculo $veiculo;

    public string $nome = '';

    public string $telefone = '';

    public ?string $email = '';

    public bool $enviado = false;

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'telefone' => ['required', 'string', 'max:20', 'regex:/^\D*(\d\D*){10,11}$/'],
            'email' => 'nullable|email|max:150',
        ];
    }

    protected function messages(): array
    {
        return [
            'telefone.regex' => 'Informe um WhatsApp válido, com DDD (ex: (14) 99999-9999).',
        ];
    }

    public function whatsappContinuarUrl(): ?string
    {
        $mensagem = "Olá! Acabei de me interessar pelo {$this->veiculo->marca} {$this->veiculo->modelo}"
            .($this->veiculo->ano_modelo ? " {$this->veiculo->ano_modelo}" : '')
            .' que vi no site. Podemos conversar?';

        return $this->veiculo->empresa?->whatsappUrl($mensagem);
    }

    public function enviar(): void
    {
        $chaveLimite = 'lead-form:'.request()->ip();

        if (cache()->get($chaveLimite, 0) >= 5) {
            $this->addError('nome', 'Muitas tentativas. Tente novamente em alguns minutos.');

            return;
        }

        $this->validate();

        $lead = Lead::create([
            'veiculo_id' => $this->veiculo->id,
            'nome' => $this->nome,
            'telefone' => $this->telefone,
            'email' => $this->email ?: null,
            'origem' => 'formulario_interesse',
            'estagio' => 'novo',
            'ip_origem' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 255),
        ]);

        (new LeadDistribuidor)->distribuir($lead);

        cache()->put($chaveLimite, cache()->get($chaveLimite, 0) + 1, now()->addMinutes(10));

        $this->reset(['nome', 'telefone', 'email']);
        $this->enviado = true;
    }

    public function render()
    {
        return view('livewire.public.interesse-form');
    }
}
