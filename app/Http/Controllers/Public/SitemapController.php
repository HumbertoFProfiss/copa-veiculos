<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\Http\Response;

/**
 * Sitemap.xml dinamico por tenant (ver prompt §4.6 - SEO). Cada revenda tem
 * seu proprio sitemap, gerado a partir do estoque real (sem cache -
 * volume por loja nao justifica a complexidade de invalidar cache aqui).
 *
 * Gerado como string pura (nao Blade) de proposito: um arquivo .blade.php
 * comecando com "<?xml" e interpretado pelo compilador Blade como abertura
 * de tag PHP ("<?" + "xml"), quebrando com ParseError.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $veiculos = Veiculo::where('status', 'disponivel')->get(['slug', 'updated_at']);

        $urls = collect([
            ['loc' => route('home'), 'lastmod' => now()->toAtomString(), 'priority' => '1.0'],
            ['loc' => route('estoque'), 'lastmod' => now()->toAtomString(), 'priority' => '0.9'],
        ])->concat($veiculos->map(fn (Veiculo $v) => [
            'loc' => route('veiculo.show', $v),
            'lastmod' => $v->updated_at->toAtomString(),
            'priority' => '0.7',
        ]));

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '    <url>'."\n";
            $xml .= '        <loc>'.e($url['loc']).'</loc>'."\n";
            $xml .= '        <lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
            $xml .= '        <priority>'.e($url['priority']).'</priority>'."\n";
            $xml .= '    </url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
