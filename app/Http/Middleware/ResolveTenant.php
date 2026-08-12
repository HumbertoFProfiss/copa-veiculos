<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lê o subdomínio da request ({slug}.copaveiculos.test local,
 * {slug}.copaveiculos.com.br em produção futura), resolve a Empresa
 * correspondente e injeta no container como singleton 'tenant'.
 *
 * Request pro domínio raiz (sem subdomínio) passa sem tenant resolvido -
 * reservado pra landing/marketing, fora do escopo desta fase. Subdomínio
 * presente mas sem empresa correspondente = 404 (não deixa "vazar" pra uma
 * empresa errada por engano).
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $central = config('tenancy.central_domain');
        $host = $request->getHost();

        if ($host === $central || $host === 'localhost' || $host === '127.0.0.1') {
            return $next($request);
        }

        $suffix = '.'.$central;

        if (! str_ends_with($host, $suffix)) {
            abort(404);
        }

        $slug = substr($host, 0, -strlen($suffix));

        $empresa = Empresa::query()->where('slug', $slug)->first();

        if (! $empresa) {
            abort(404);
        }

        app()->instance('tenant', $empresa);

        return $next($request);
    }
}
