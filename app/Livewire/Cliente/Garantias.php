<?php

namespace App\Livewire\Cliente;

use App\Models\Venda;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Garantias extends Component
{
    public function render()
    {
        $compras = Auth::guard('cliente')->user()
            ->vendas()
            ->with(['veiculo', 'garantiasChamados' => fn ($q) => $q->latest()])
            ->where('status', 'confirmada')
            ->latest('data_venda')
            ->get()
            ->map(function (Venda $venda) {
                $venda->garantia_ativa = $venda->data_entrega
                    && now()->lt($venda->data_entrega->copy()->addDays($venda->prazo_garantia_dias));

                return $venda;
            });

        return view('livewire.cliente.garantias', compact('compras'))
            ->layout('layouts.cliente', ['title' => 'Garantias']);
    }
}
