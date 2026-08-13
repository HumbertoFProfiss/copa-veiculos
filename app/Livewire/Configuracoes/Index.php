<?php

namespace App\Livewire\Configuracoes;

use App\Services\Ia\AiProviderFactory;
use Livewire\Component;

/**
 * Painel de configurações da empresa. Além dos dados cadastrais, mostra
 * claramente o status de cada integração que depende de credencial/
 * homologação externa (NF-e, Renave, WhatsApp, assinatura eletrônica,
 * MultiBanco) - nenhuma delas é funcional nesta fase, mas o usuário precisa
 * ENXERGAR isso no painel em vez de descobrir por tentativa e erro.
 */
class Index extends Component
{
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
