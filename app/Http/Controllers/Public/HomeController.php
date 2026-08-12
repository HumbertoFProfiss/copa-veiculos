<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $destaques = Veiculo::where('status', 'disponivel')
            ->where('destaque', true)
            ->with('fotos')
            ->latest()
            ->take(6)
            ->get();

        $ultimasAdicoes = Veiculo::where('status', 'disponivel')
            ->with('fotos')
            ->latest()
            ->take(8)
            ->get();

        return view('public.home', compact('destaques', 'ultimasAdicoes'));
    }
}
