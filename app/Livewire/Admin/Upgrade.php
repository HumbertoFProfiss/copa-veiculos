<?php

namespace App\Livewire\Admin;

use Livewire\Component;

/**
 * Tela de bloqueio exibida quando EnsureModuloAtivo nega acesso a um modulo
 * fora do plano contratado (ver prompt §5 - "tela de upgrade").
 */
class Upgrade extends Component
{
    public string $modulo = '';

    public function mount(string $modulo = ''): void
    {
        $this->modulo = $modulo;
    }

    public function render()
    {
        return view('livewire.admin.upgrade')
            ->layout('layouts.admin', ['title' => 'Upgrade necessário']);
    }
}
