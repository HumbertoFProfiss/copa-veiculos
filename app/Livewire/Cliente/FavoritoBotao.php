<?php

namespace App\Livewire\Cliente;

use App\Models\ClienteFavorito;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Botão de favoritar embutido no card/detalhe do veículo. Só funciona
 * logado (guarda "cliente", separada da equipe da revenda) - deslogado,
 * o clique leva pro login em vez de favoritar silenciosamente.
 */
class FavoritoBotao extends Component
{
    public Veiculo $veiculo;

    public bool $favoritado = false;

    public function mount(): void
    {
        if (Auth::guard('cliente')->check()) {
            $this->favoritado = ClienteFavorito::where('cliente_id', Auth::guard('cliente')->id())
                ->where('veiculo_id', $this->veiculo->id)
                ->exists();
        }
    }

    public function alternar(): void
    {
        if (! Auth::guard('cliente')->check()) {
            $this->redirect(route('cliente.login'));

            return;
        }

        $favorito = ClienteFavorito::where('cliente_id', Auth::guard('cliente')->id())
            ->where('veiculo_id', $this->veiculo->id)
            ->first();

        if ($favorito) {
            $favorito->delete();
            $this->favoritado = false;
        } else {
            ClienteFavorito::create([
                'cliente_id' => Auth::guard('cliente')->id(),
                'veiculo_id' => $this->veiculo->id,
            ]);
            $this->favoritado = true;
        }
    }

    public function render()
    {
        return view('livewire.cliente.favorito-botao');
    }
}
