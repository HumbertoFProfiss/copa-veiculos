<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra a caixa de comparacao com a fipe quando ha fipe + compra ou venda preenchidos', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('preco_tabela_fipe', 30000)
        ->set('preco_compra', 25000)
        ->set('preco_venda', 32000)
        ->assertSee('Comparação com a Tabela FIPE')
        ->assertSee('-16,7%')
        ->assertSee('+6,7%');
});

it('nao mostra a caixa quando so tem fipe, sem compra nem venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)
        ->test(Form::class)
        ->set('preco_tabela_fipe', 30000)
        ->assertDontSee('Comparação com a Tabela FIPE');
});
