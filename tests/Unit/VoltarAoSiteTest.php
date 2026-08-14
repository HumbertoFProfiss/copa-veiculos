<?php

use App\Models\Empresa;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra o botao voltar ao site no topo do menu do admin, apontando pra home publica do tenant', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $resposta = $this->actingAs($user)
        ->withServerVariables(['HTTP_HOST' => 'empresa-a.'.config('tenancy.central_domain')])
        ->get(route('dashboard'));

    $resposta->assertOk()
        ->assertSee('Voltar ao site');
});
