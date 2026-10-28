<?php

use App\Livewire\Vendas\Nova;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('aceita financiamento como forma de pagamento e salva o registro de pagamento', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaFinanciadoTeste'.Str::random(6),
        'modelo' => 'F',
        'slug' => Str::slug('venda-financiado-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Financiado Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('pagamentos.0.forma', 'financiamento')
        ->set('pagamentos.0.valor', 50000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and($venda->forma_pagamento)->toBe('financiamento')
        ->and($venda->pagamentos()->count())->toBe(1)
        ->and($venda->pagamentos()->first()->forma_pagamento)->toBe('financiamento')
        ->and((float) $venda->pagamentos()->first()->valor)->toBe(50000.0);

    $venda->pagamentos()->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('permite combinar duas formas de pagamento numa mesma venda e marca a venda como mista', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaMistaTeste'.Str::random(6),
        'modelo' => 'M',
        'slug' => Str::slug('venda-mista-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Mista Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('pagamentos.0.forma', 'dinheiro')
        ->set('pagamentos.0.valor', 10000)
        ->call('adicionarPagamento')
        ->set('pagamentos.1.forma', 'financiamento')
        ->set('pagamentos.1.valor', 40000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and($venda->forma_pagamento)->toBe('misto')
        ->and($venda->pagamentos()->count())->toBe(2)
        ->and((float) $venda->pagamentos()->sum('valor'))->toBe(50000.0);

    $venda->pagamentos()->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('erra quando a soma das formas de pagamento nao bate com o valor da venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaSomaErradaTeste'.Str::random(6),
        'modelo' => 'E',
        'slug' => Str::slug('venda-soma-errada-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Soma Errada Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('pagamentos.0.forma', 'dinheiro')
        ->set('pagamentos.0.valor', 30000)
        ->call('salvar')
        ->assertHasErrors(['pagamentos']);

    expect(Venda::where('veiculo_id', $veiculo->id)->exists())->toBeFalse();

    $veiculo->delete();
    $cliente->delete();
});

it('salva lucro financeiro, instituicao financeira e impostos na venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaLucroFinanceiroTeste'.Str::random(6),
        'modelo' => 'L',
        'slug' => Str::slug('venda-lucro-financeiro-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Lucro Financeiro Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('pagamentos.0.forma', 'financiamento')
        ->set('pagamentos.0.valor', 50000)
        ->set('lucro_financeiro', 1200)
        ->set('instituicao_financeira', 'Banco Teste')
        ->set('impostos', 350)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and((float) $venda->lucro_financeiro)->toBe(1200.0)
        ->and($venda->instituicao_financeira)->toBe('Banco Teste')
        ->and((float) $venda->impostos)->toBe(350.0);

    $venda->pagamentos()->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
