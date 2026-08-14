<?php

use App\Models\Empresa;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra a foto da fachada na secao Sobre da home publica', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);

    $resposta = $this->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('home'));

    $resposta->assertOk()
        ->assertSee('fachada.jpg', false);
});
