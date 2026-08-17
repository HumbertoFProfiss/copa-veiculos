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

it('mostra os campos de entrada e valor financiado so quando a forma de pagamento financiado e selecionada', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Nova::class)
        ->assertDontSee('Valor financiado (R$)')
        ->set('forma_pagamento', 'financiado')
        ->assertSee('Valor de entrada (R$)')
        ->assertSee('Valor financiado (R$)')
        ->set('forma_pagamento', 'avista')
        ->assertDontSee('Valor financiado (R$)');
});

it('calcula entrada e valor financiado automaticamente um a partir do outro', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaFinanciadoTeste'.Str::random(6),
        'modelo' => 'F',
        'slug' => Str::slug('venda-financiado-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 84500,
    ]);

    $componente = Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('forma_pagamento', 'financiado')
        ->set('valor_financiado', 70000)
        ->assertSet('valor_entrada', 14500.0);

    $componente->set('valor_entrada', 20000)
        ->assertSet('valor_financiado', 64500.0);

    $veiculo->delete();
});

it('exige entrada e valor financiado quando a forma de pagamento e financiado, e salva os valores na venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaFinanciadoOkTeste'.Str::random(6),
        'modelo' => 'F',
        'slug' => Str::slug('venda-financiado-ok-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Financiado Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('forma_pagamento', 'financiado')
        ->call('salvar')
        ->assertHasErrors(['valor_entrada', 'valor_financiado']);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->set('forma_pagamento', 'financiado')
        ->set('valor_financiado', 40000)
        ->call('salvar')
        ->assertHasNoErrors();

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and((float) $venda->valor_entrada)->toBe(10000.0)
        ->and((float) $venda->valor_financiado)->toBe(40000.0);

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('nao salva valor de entrada/financiado quando a forma de pagamento nao e financiado', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaAvistaTeste'.Str::random(6),
        'modelo' => 'A',
        'slug' => Str::slug('venda-avista-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 30000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Venda Avista Teste '.Str::random(4)]);

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
        ->and($venda->valor_entrada)->toBeNull()
        ->and($venda->valor_financiado)->toBeNull();

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
