<?php

namespace App\Livewire\Configuracoes;

use App\Services\Ia\AiProviderFactory;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Painel de configurações da empresa. Além do status de cada integração
 * externa (NF-e, Renave, WhatsApp...), permite editar os dados de contato
 * usados na home pública (endereço, telefone, WhatsApp, redes sociais) -
 * sem isso, o site público não tem o que mostrar na seção de localização.
 */
class Index extends Component
{
    #[Validate('nullable|string|max:20')]
    public ?string $telefone = '';

    #[Validate('nullable|string|max:20')]
    public ?string $whatsapp = '';

    #[Validate('nullable|email|max:150')]
    public ?string $email_contato = '';

    #[Validate('nullable|string|max:255')]
    public ?string $endereco = '';

    #[Validate('nullable|string|max:100')]
    public ?string $cidade = '';

    #[Validate('nullable|string|max:2')]
    public ?string $uf = '';

    #[Validate('nullable|string|max:150')]
    public ?string $horario_funcionamento = '';

    #[Validate('nullable|url|max:255')]
    public ?string $instagram_url = '';

    #[Validate('nullable|url|max:255')]
    public ?string $facebook_url = '';

    #[Validate('nullable|string|max:1000')]
    public ?string $sobre_texto = '';

    public function mount(): void
    {
        $empresa = app('tenant');

        $this->telefone = $empresa->telefone;
        $this->whatsapp = $empresa->whatsapp;
        $this->email_contato = $empresa->email_contato;
        $this->endereco = $empresa->endereco;
        $this->cidade = $empresa->cidade;
        $this->uf = $empresa->uf;
        $this->horario_funcionamento = $empresa->horario_funcionamento;
        $this->instagram_url = $empresa->instagram_url;
        $this->facebook_url = $empresa->facebook_url;
        $this->sobre_texto = $empresa->sobre_texto;
    }

    public function salvar(): void
    {
        $this->authorize('configuracoes.editar');

        $dados = $this->validate();

        app('tenant')->update($dados);

        session()->flash('sucesso', 'Dados salvos. A home pública já reflete as mudanças.');
    }

    public function render()
    {
        $ia = AiProviderFactory::make();

        return view('livewire.configuracoes.index', [
            'empresa' => app('tenant'),
            'iaConfigurada' => $ia->disponivel(),
            'iaProvider' => config('services.ia.provider'),
            'iaModelo' => config('services.ia.modelo'),
        ])->layout('layouts.admin', ['title' => 'Configurações']);
    }
}
