<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolve a empresa (tenant) da request em duas etapas:
 *
 * 1. Domínio próprio: se o host bater exatamente com `empresas.dominio_customizado`
 *    de alguma empresa, usa essa empresa direto - é assim que o domínio real de
 *    uma revenda (ex: worldcred.com.br pra Copa Veículos) funciona sem precisar
 *    de subdomínio.
 * 2. Subdomínio: {slug}.{central_domain} ({slug}.copaveiculos.test local,
 *    {slug}.copaveiculos.com.br em produção futura) - usado pra revendas que
 *    ainda não têm domínio próprio configurado.
 *
 * Request pro domínio raiz central (sem subdomínio nem domínio customizado
 * correspondente) passa sem tenant resolvido - reservado pra landing/marketing,
 * fora do escopo desta fase. Subdomínio presente mas sem empresa correspondente
 * = 404 (não deixa "vazar" pra uma empresa errada por engano).
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        $empresa = Empresa::query()->where('dominio_customizado', $host)->first();

        if ($empresa) {
            app()->instance('tenant', $empresa);

            return $next($request);
        }

        $central = config('tenancy.central_domain');

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
