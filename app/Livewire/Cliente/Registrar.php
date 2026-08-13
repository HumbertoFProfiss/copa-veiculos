<?php

namespace App\Livewire\Cliente;

use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Registrar extends Component
{
    public string $nome = '';

    public string $email = '';

    public ?string $telefone = '';

    public string $password = '';

    public string $password_confirmation = '';

    protected function rules(): array
    {
        return [
            'nome' => 'required|string|max:150',
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('clientes', 'email')->where('empresa_id', app('tenant')->id),
            ],
            'telefone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function registrar(): void
    {
        $dados = $this->validate();

        $cliente = Cliente::create([
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'telefone' => $dados['telefone'] ?? null,
            'password' => Hash::make($dados['password']),
        ]);

        Auth::guard('cliente')->login($cliente);
        session()->regenerate();

        $this->redirect(route('cliente.dashboard'));
    }

    public function render()
    {
        return view('livewire.cliente.registrar')
            ->layout('layouts.cliente', ['title' => 'Criar conta']);
    }
}
