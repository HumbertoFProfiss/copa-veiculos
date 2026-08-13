<?php

namespace App\Livewire\Cliente;

use App\Models\ClienteFavorito;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Favoritos extends Component
{
    public function remover(int $favoritoId): void
    {
        ClienteFavorito::where('cliente_id', Auth::guard('cliente')->id())
            ->where('id', $favoritoId)
            ->delete();
    }

    public function render()
    {
        $favoritos = Auth::guard('cliente')->user()
            ->favoritos()
            ->with(['veiculo.fotos'])
            ->latest()
            ->get()
            ->filter(fn (ClienteFavorito $f) => $f->veiculo !== null);

        return view('livewire.cliente.favoritos', compact('favoritos'))
            ->layout('layouts.cliente', ['title' => 'Favoritos']);
    }
}
