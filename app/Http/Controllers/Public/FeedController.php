<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Canal;
use App\Models\Publicacao;
use App\Services\AdCanais\AdCanalAdapterResolver;
use Illuminate\Http\Response;

/**
 * Feed público por canal (ver plano §7: "/feeds/{loja}/{canal}.csv" - o
 * "{loja}" já é resolvido pelo subdomínio, não precisa repetir na URL).
 * Gerado sob demanda a partir do banco (sempre atual, sem cron de
 * regeneração de arquivo estático necessário nesta fase).
 */
class FeedController extends Controller
{
    public function __invoke(string $canalSlug, AdCanalAdapterResolver $resolver): Response
    {
        $canal = Canal::where('slug', $canalSlug)->firstOrFail();
        $adapter = $resolver->resolve($canal);

        $veiculos = Publicacao::where('canal_id', $canal->id)
            ->where('status', 'publicado')
            ->with('veiculo.fotos', 'veiculo.opcionais')
            ->get()
            ->pluck('veiculo')
            ->filter();

        $linhas = $veiculos->map(fn ($veiculo) => $adapter->montarPayload($veiculo));

        if ($linhas->isEmpty()) {
            $csv = '';
        } else {
            $cabecalho = array_keys($linhas->first());
            $out = fopen('php://temp', 'r+');
            fputcsv($out, $cabecalho, ';');
            foreach ($linhas as $linha) {
                fputcsv($out, $linha, ';');
            }
            rewind($out);
            $csv = stream_get_contents($out);
            fclose($out);
        }

        return response($csv, 200)->header('Content-Type', 'text/csv; charset=UTF-8');
    }
}
