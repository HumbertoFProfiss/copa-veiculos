<?php

use App\Models\Empresa;
use App\Models\Venda;
use App\Services\Contratos\ContratoRenderer;
use Database\Seeders\ContratoModeloSeeder;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('semeia pelo menos 15 modelos de contrato e todos renderizam sem variavel nao resolvida', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    (new ContratoModeloSeeder)->run();

    $modelos = \App\Models\ContratoModelo::where('empresa_id', $empresa->id)->get();
    expect($modelos->count())->toBeGreaterThanOrEqual(15);

    $venda = Venda::where('empresa_id', $empresa->id)->first();
    expect($venda)->not->toBeNull('precisa de ao menos 1 venda seedada pra empresa de teste');

    $renderer = new ContratoRenderer;

    $modelosComVariavelQuebrada = $modelos->filter(function ($modelo) use ($renderer, $venda, $empresa) {
        return str_contains($renderer->renderizar($modelo->corpo_html, $venda, $empresa), '{{');
    })->pluck('tipo');

    expect($modelosComVariavelQuebrada)->toBeEmpty();
});
