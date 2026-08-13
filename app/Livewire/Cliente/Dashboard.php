<?php

namespace App\Livewire\Cliente;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Dashboard extends Component
{
    public string $nome = '';

    public ?string $email = '';

    public ?string $telefone = '';

    public ?string $endereco = '';

    public ?string $cidade = '';

    public ?string $uf = '';

    public ?string $novaSenha = '';

    public ?string $novaSenha_confirmation = '';

    public function mount(): void
    {
        $cliente = Auth::guard('cliente')->user();

        $this->nome = $cliente->nome;
        $this->email = $cliente->email;
        $this->telefone = $cliente->telefone;
        $this->endereco = $cliente->endereco;
        $this->cidade = $cliente->cidade;
        $this->uf = $cliente->uf;
    }

    public function salvar(): void
    {
        $dados = $this->validate([
            'nome' => 'required|string|max:150',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'uf' => 'nullable|string|max:2',
            'novaSenha' => 'nullable|string|min:6|confirmed',
        ]);

        $cliente = Auth::guard('cliente')->user();

        $atualizar = collect($dados)->except(['novaSenha', 'novaSenha_confirmation'])->toArray();

        if (filled($dados['novaSenha'] ?? null)) {
            $atualizar['password'] = Hash::make($dados['novaSenha']);
        }

        $cliente->update($atualizar);
        $this->reset(['novaSenha', 'novaSenha_confirmation']);

        session()->flash('sucesso', 'Dados atualizados.');
    }

    public function render()
    {
        $cliente = Auth::guard('cliente')->user();

        return view('livewire.cliente.dashboard', [
            'totalFavoritos' => $cliente->favoritos()->count(),
            'totalCompras' => $cliente->vendas()->where('status', 'confirmada')->count(),
            'garantiasAbertas' => $cliente->garantiasChamados()->whereNotIn('status', ['concluido', 'recusado'])->count(),
        ])->layout('layouts.cliente', ['title' => 'Meu Perfil']);
    }
}
