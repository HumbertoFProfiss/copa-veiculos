<?php

namespace App\Livewire\Configuracoes;

use App\Models\Empresa;
use App\Models\Fatura;
use App\Services\Ia\AiProviderFactory;
use Illuminate\Validation\Rule;
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

    public ?string $dominio_customizado = '';

    #[Validate('nullable|string|max:30')]
    public ?string $analytics_ga4_id = '';

    #[Validate('nullable|string|max:30')]
    public ?string $analytics_gtm_id = '';

    #[Validate('nullable|string|max:30')]
    public ?string $analytics_meta_pixel_id = '';

    #[Validate('required|integer|min:1|max:50')]
    public int $limite_destaques = 6;

    #[Validate('required|integer|min:1|max:99')]
    public int $abc_limite_a = 80;

    #[Validate('required|integer|min:2|max:100')]
    public int $abc_limite_b = 95;

    public function mount(): void
    {
        $empresa = app('tenant');

        $this->dominio_customizado = $empresa->dominio_customizado;
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
        $this->analytics_ga4_id = $empresa->analytics_ga4_id;
        $this->analytics_gtm_id = $empresa->analytics_gtm_id;
        $this->analytics_meta_pixel_id = $empresa->analytics_meta_pixel_id;
        $this->limite_destaques = $empresa->limite_destaques;
        $this->abc_limite_a = $empresa->abc_limite_a;
        $this->abc_limite_b = $empresa->abc_limite_b;
    }

    public function salvar(): void
    {
        $this->authorize('configuracoes.editar');

        if ($this->abc_limite_a >= $this->abc_limite_b) {
            $this->addError('abc_limite_a', 'O limite da Classe A precisa ser menor que o da Classe B.');

            return;
        }

        $dados = $this->validate() + $this->validate([
            'dominio_customizado' => [
                'nullable', 'string', 'max:255', 'regex:/^[a-z0-9.-]+\.[a-z]{2,}$/i',
                Rule::unique('empresas', 'dominio_customizado')->ignore(app('tenant')->id),
            ],
        ]);

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
            'precoPlanoAtual' => Empresa::PRECOS_PLANOS[app('tenant')->plano] ?? null,
            'faturas' => Fatura::orderByDesc('referencia')->limit(12)->get(),
        ])->layout('layouts.admin', ['title' => 'Configurações']);
    }
}
