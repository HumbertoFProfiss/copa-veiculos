<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\View\View;

class VeiculoController extends Controller
{
    public function __invoke(Veiculo $veiculo): View
    {
        abort_if($veiculo->status !== 'disponivel', 404);

        $veiculo->load(['fotos', 'videos', 'opcionais']);

        $semelhantes = Veiculo::where('status', 'disponivel')
            ->where('marca', $veiculo->marca)
            ->where('id', '!=', $veiculo->id)
            ->with('fotos')
            ->take(4)
            ->get();

        $mesmaMarca = $semelhantes->isNotEmpty();

        // Sem outro veiculo da mesma marca no estoque: mostra os mais
        // recentes em geral, em vez de deixar a pagina sem nenhuma sugestao.
        if (! $mesmaMarca) {
            $semelhantes = Veiculo::where('status', 'disponivel')
                ->where('id', '!=', $veiculo->id)
                ->with('fotos')
                ->latest()
                ->take(4)
                ->get();
        }

        return view('public.veiculo', compact('veiculo', 'semelhantes', 'mesmaMarca'));
    }
}
