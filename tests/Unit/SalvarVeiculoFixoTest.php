<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('o botao Salvar fica fora do form, sempre visivel (barra fixa), mas o Enter no formulario ainda funciona', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(Form::class)->html();

    // a barra fixa com o botao visivel de verdade fica fora do <form>
    expect($html)->toContain('wire:click="salvar"')
        ->and($html)->toContain('sticky bottom-0');

    // o form ainda tem um submit control (mesmo que escondido) pra Enter continuar funcionando
    expect($html)->toContain('type="submit" class="hidden"');
});
