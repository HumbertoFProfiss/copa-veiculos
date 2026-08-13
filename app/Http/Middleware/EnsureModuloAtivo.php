<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloqueia acesso a um modulo nao incluido no plano contratado da empresa
 * (ver prompt §5 - "bloquear acesso ao modulo nao contratado com tela de
 * upgrade"). Uso: ->middleware('modulo:clientes').
 */
class EnsureModuloAtivo
{
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        $empresa = app('tenant');

        if (! $empresa->possuiAcessoModulo($modulo)) {
            return redirect()->route('admin.upgrade', ['modulo' => $modulo]);
        }

        return $next($request);
    }
}
