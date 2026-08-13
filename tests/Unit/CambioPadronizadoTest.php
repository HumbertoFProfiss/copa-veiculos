<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('aceita uma opcao valida de cambio e rejeita texto fora da lista padronizada', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Form::class)
        ->set('marca', 'CambioTeste'.Str::random(6))
        ->set('modelo', 'X')
        ->set('km', 0)
        ->set('cambio', 'Turbinado')
        ->call('salvar')
        ->assertHasErrors('cambio');

    $marca = 'CambioTeste'.Str::random(6);
    Livewire::actingAs($user)->test(Form::class)
        ->set('marca', $marca)
        ->set('modelo', 'X')
        ->set('km', 0)
        ->set('cambio', 'CVT')
        ->call('salvar')
        ->assertHasNoErrors('cambio');

    Veiculo::where('marca', $marca)->first()->delete();
});
