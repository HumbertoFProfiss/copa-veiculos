<?php

namespace App\Livewire\Cliente;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $lembrar = false;

    public function entrar(): void
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $chaveLimite = 'cliente-login:'.request()->ip();
        if (cache()->get($chaveLimite, 0) >= 8) {
            $this->addError('email', 'Muitas tentativas. Tente novamente em alguns minutos.');

            return;
        }

        if (! Auth::guard('cliente')->attempt(['email' => $this->email, 'password' => $this->password], $this->lembrar)) {
            cache()->put($chaveLimite, cache()->get($chaveLimite, 0) + 1, now()->addMinutes(10));
            $this->addError('email', 'E-mail ou senha incorretos.');

            return;
        }

        session()->regenerate();

        $this->redirect(route('cliente.dashboard'));
    }

    public function render()
    {
        return view('livewire.cliente.login')
            ->layout('layouts.cliente', ['title' => 'Entrar']);
    }
}
