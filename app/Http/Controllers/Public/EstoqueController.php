<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstoqueController extends Controller
{
    public function __invoke(Request $request): View
    {
        $veiculos = Veiculo::query()
            ->where('status', 'disponivel')
            ->with('fotos')
            ->when($request->filled('marca'), fn ($q) => $q->where('marca', $request->string('marca')))
            ->when($request->filled('modelo'), fn ($q) => $q->where('modelo', 'like', '%'.$request->string('modelo').'%'))
            ->when($request->filled('cambio'), fn ($q) => $q->where('cambio', $request->string('cambio')))
            ->when($request->filled('combustivel'), fn ($q) => $q->where('combustivel', $request->string('combustivel')))
            ->when($request->filled('ano_min'), fn ($q) => $q->where('ano_modelo', '>=', $request->integer('ano_min')))
            ->when($request->filled('ano_max'), fn ($q) => $q->where('ano_modelo', '<=', $request->integer('ano_max')))
            ->when($request->filled('preco_min'), fn ($q) => $q->where('preco_venda', '>=', $request->float('preco_min')))
            ->when($request->filled('preco_max'), fn ($q) => $q->where('preco_venda', '<=', $request->float('preco_max')))
            ->orderBy('destaque', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $marcasDisponiveis = Veiculo::where('status', 'disponivel')->distinct()->pluck('marca')->sort()->values();

        return view('public.estoque', compact('veiculos', 'marcasDisponiveis'));
    }
}
