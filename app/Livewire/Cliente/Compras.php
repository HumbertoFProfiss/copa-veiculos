<?php

namespace App\Livewire\Cliente;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Compras extends Component
{
    public function render()
    {
        $compras = Auth::guard('cliente')->user()
            ->vendas()
            ->with('veiculo')
            ->whereIn('status', ['confirmada', 'cancelada'])
            ->latest('data_venda')
            ->get();

        return view('livewire.cliente.compras', compact('compras'))
            ->layout('layouts.cliente', ['title' => 'Minhas Compras']);
    }
}
