<?php

use App\Livewire\Veiculos\Form;
use App\Models\Empresa;
use App\Models\Veiculo;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('esconde o valor de compra quando o veiculo e consignado, mas mostra quando e proprio', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    // O aviso explicativo pra consignado cita "Valor de compra" entre aspas
    // de proposito, entao a checagem precisa ser pelo campo em si (o
    // wire:model do input), nao pelo texto solto.
    $component = Livewire::actingAs($user)->test(Form::class)
        ->set('tipo_propriedade', 'proprio')
        ->assertSee('wire:model.live.debounce.400ms="preco_compra"', false);

    $component->set('tipo_propriedade', 'consignado')
        ->assertDontSee('wire:model.live.debounce.400ms="preco_compra"', false)
        ->assertSee('não tem "Valor de compra"', false);
});

it('sincroniza automaticamente comissao em R$ e em % nos dois sentidos', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $component = Livewire::actingAs($user)->test(Form::class)
        ->set('tipo_propriedade', 'consignado')
        ->set('preco_venda', 50000)
        ->set('consignado_comissao_pct', 10)
        ->assertSet('consignado_comissao_rs', 5000.0)
        ->assertSet('consignado_comissao_tipo', 'percentual');

    $component->set('consignado_comissao_rs', 2500)
        ->assertSet('consignado_comissao_pct', 5.0)
        ->assertSet('consignado_comissao_tipo', 'fixa');
});

it('calcula a margem do veiculo consignado pela comissao, nao por venda menos compra', function () {
    $veiculo = new Veiculo([
        'tipo_propriedade' => 'consignado',
        'preco_venda' => 50000,
        'consignado_comissao_tipo' => 'percentual',
        'consignado_comissao_valor' => 10,
    ]);

    expect($veiculo->margem())->toBe(5000.0);
});
