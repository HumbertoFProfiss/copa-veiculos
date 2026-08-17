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

it('mostra os campos de veiculo de troca so quando a forma de pagamento troca e selecionada', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Nova::class)
        ->assertDontSee('Veículo dado como troca')
        ->set('forma_pagamento', 'troca')
        ->assertSee('Veículo dado como troca')
        ->assertSee('Valor de volta (avaliação)')
        ->set('forma_pagamento', 'avista')
        ->assertDontSee('Veículo dado como troca');
});

it('exige marca, modelo e valor avaliado do carro de troca quando a forma de pagamento e troca', function () {
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
        ->set('forma_pagamento', 'troca')
        ->call('salvar')
        ->assertHasErrors(['troca_marca', 'troca_modelo', 'troca_valor_avaliado']);

    $veiculo->delete();
    $cliente->delete();
});

it('cria o registro do carro de troca ao salvar uma venda com forma de pagamento troca', function () {
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
        ->set('forma_pagamento', 'troca')
        ->set('troca_marca', 'Fiat')
        ->set('troca_modelo', 'Uno')
        ->set('troca_ano_modelo', 2015)
        ->set('troca_placa', 'ABC1D23')
        ->set('troca_km', 80000)
        ->set('troca_valor_avaliado', 18000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull();

    $troca = CarroTroca::where('venda_id', $venda->id)->first();
    expect($troca)->not->toBeNull()
        ->and($troca->marca)->toBe('Fiat')
        ->and($troca->modelo)->toBe('Uno')
        ->and((int) $troca->ano_modelo)->toBe(2015)
        ->and((float) $troca->valor_avaliado)->toBe(18000.0);

    $troca->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('nao cria carro de troca quando a forma de pagamento nao e troca', function () {
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
        ->set('forma_pagamento', 'avista')
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and(CarroTroca::where('venda_id', $venda->id)->count())->toBe(0);

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
