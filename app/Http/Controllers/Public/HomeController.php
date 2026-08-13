<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $empresa = app('tenant');

        $destaques = Veiculo::where('status', 'disponivel')
            ->where('destaque', true)
            ->with('fotos')
            ->latest()
            ->take($empresa->limite_destaques ?: 6)
            ->get();

        $ultimasAdicoes = Veiculo::where('status', 'disponivel')
            ->with('fotos')
            ->latest()
            ->take(3)
            ->get();

        return view('public.home', compact('destaques', 'ultimasAdicoes', 'empresa'));
    }
}
