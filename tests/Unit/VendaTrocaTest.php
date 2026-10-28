<?php

use App\Livewire\Vendas\Nova;
use App\Models\CarroTroca;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra os campos de veiculo de troca so quando uma forma de pagamento troca e selecionada', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Nova::class)
        ->assertDontSee('Veículo dado como troca')
        ->set('pagamentos.0.forma', 'troca')
        ->assertSee('Veículo dado como troca')
        ->assertSee('Valor de volta (avaliação)')
        ->set('pagamentos.0.forma', 'dinheiro')
        ->assertDontSee('Veículo dado como troca');
});

it('exige marca, modelo e valor avaliado do carro de troca quando uma forma de pagamento e troca', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaTrocaTeste'.Str::random(6),
        'modelo' => 'T',
        'slug' => Str::slug('venda-troca-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 45000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Troca Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 45000)
        ->set('pagamentos.0.forma', 'troca')
        ->set('pagamentos.0.valor', 45000)
        ->call('salvar')
        ->assertHasErrors(['troca_marca', 'troca_modelo', 'troca_valor_avaliado']);

    $veiculo->delete();
    $cliente->delete();
});

it('cria o registro do carro de troca ao salvar uma venda com uma forma de pagamento troca', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaTrocaOkTeste'.Str::random(6),
        'modelo' => 'T',
        'slug' => Str::slug('venda-troca-ok-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 45000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Troca Ok Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 45000)
        ->set('pagamentos.0.forma', 'troca')
        ->set('pagamentos.0.valor', 45000)
        ->set('troca_marca', 'Fiat')
        ->set('troca_modelo', 'Uno')
        ->set('troca_ano_modelo', 2015)
        ->set('troca_placa', 'ABC1D23')
        ->set('troca_km', 80000)
        ->set('troca_valor_avaliado', 18000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and($venda->forma_pagamento)->toBe('troca');

    $troca = CarroTroca::where('venda_id', $venda->id)->first();
    expect($troca)->not->toBeNull()
        ->and($troca->marca)->toBe('Fiat')
        ->and($troca->modelo)->toBe('Uno')
        ->and((int) $troca->ano_modelo)->toBe(2015)
        ->and((float) $troca->valor_avaliado)->toBe(18000.0);

    expect($venda->pagamentos()->count())->toBe(1)
        ->and((float) $venda->pagamentos()->first()->valor)->toBe(45000.0);

    $troca->delete();
    $venda->pagamentos()->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('nao cria carro de troca quando nenhuma forma de pagamento e troca', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaSemTrocaTeste'.Str::random(6),
        'modelo' => 'S',
        'slug' => Str::slug('venda-sem-troca-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 30000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Sem Troca Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 30000)
        ->set('pagamentos.0.forma', 'dinheiro')
        ->set('pagamentos.0.valor', 30000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and(CarroTroca::where('venda_id', $venda->id)->count())->toBe(0);

    $venda->pagamentos()->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
