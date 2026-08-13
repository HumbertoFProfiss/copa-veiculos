<?php

use App\Models\Empresa;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra os atalhos rapidos e nao mostra mais a contagem de veiculos em estoque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('home'));

    $resposta->assertOk()
        ->assertSee('Sobre nós')
        ->assertSee('Simular Financiamento')
        ->assertSee('Venda seu Carro')
        ->assertSee('Onde Estamos')
        ->assertDontSee('veículos em estoque');
});

it('mostra o atalho de redes sociais e os links no rodape da secao onde estamos quando configurados', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $instagramOriginal = $empresa->instagram_url;
    $empresa->update(['instagram_url' => 'https://instagram.com/testeempresa']);

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('home'));

    $resposta->assertOk()
        ->assertSee('Redes Sociais')
        ->assertSee('https://instagram.com/testeempresa', false);

    $empresa->update(['instagram_url' => $instagramOriginal]);
});
