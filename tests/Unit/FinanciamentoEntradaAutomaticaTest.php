<?php

use App\Livewire\Vendas\Show;
use App\Models\Banco;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('calcula a entrada automaticamente quando o valor financiado e preenchido, e vice-versa', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'EntradaAutoTeste'.Str::random(6),
        'modelo' => 'E',
        'slug' => Str::slug('entrada-auto-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 84500,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Entrada Auto Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'financiado', 'preco_venda' => 84500, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    $componente = Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('abrirFormFinanciamento')
        ->set('financiamento_valor_financiado', 70000)
        ->assertSet('financiamento_entrada', 14500.0);

    $componente->set('financiamento_entrada', 20000)
        ->assertSet('financiamento_valor_financiado', 64500.0);

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('a soma de entrada e valor financiado calculados bate com o total da venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'EntradaSomaTeste'.Str::random(6),
        'modelo' => 'S',
        'slug' => Str::slug('entrada-soma-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Entrada Soma Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'financiado', 'preco_venda' => 50000, 'desconto' => 2000,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    $banco = Banco::where('ativo', true)->first();

    $componente = Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('abrirFormFinanciamento')
        ->set('financiamento_valor_financiado', 40000);

    expect($componente->get('financiamento_entrada'))->toBe(8000.0); // 50000 - 2000 - 40000

    $componente->set('financiamento_banco_id', $banco->id)
        ->set('financiamento_num_parcelas', 36)
        ->call('simularFinanciamento')
        ->assertHasNoErrors();

    $proposta = $venda->propostasFinanciamento()->first();
    expect($proposta)->not->toBeNull()
        ->and((float) $proposta->valor_financiado)->toBe(40000.0)
        ->and((float) $proposta->entrada)->toBe(8000.0);

    $proposta->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
