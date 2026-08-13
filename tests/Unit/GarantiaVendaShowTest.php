<?php

use App\Livewire\Vendas\Show;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\GarantiaChamado;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('mostra os detalhes da venda com link a partir da listagem', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'GarantiaTeste'.Str::random(6),
        'modelo' => 'G',
        'slug' => Str::slug('garantia-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_compra' => 40000, 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Garantia Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 50000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    Livewire::actingAs($user)->test(\App\Livewire\Vendas\Index::class)
        ->assertSee('Ver detalhes');

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->assertSee($veiculo->marca)
        ->assertSee($cliente->nome);

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('registra um chamado de garantia aprovado e o custo entra na margem do veiculo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'GarantiaCustoTeste'.Str::random(6),
        'modelo' => 'G',
        'slug' => Str::slug('garantia-custo-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_compra' => 40000, 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Garantia Custo '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 50000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    expect($veiculo->fresh()->margem())->toBe(10000.0);

    $component = Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('novaGarantia')
        ->set('descricao_problema', 'Ar-condicionado não gela')
        ->set('status', 'aprovado')
        ->set('custo_peca', 300)
        ->set('custo_servico', 150)
        ->call('salvarGarantia')
        ->assertHasNoErrors();

    $chamado = GarantiaChamado::where('venda_id', $venda->id)->first();
    expect($chamado)->not->toBeNull()
        ->and($chamado->veiculo_id)->toBe($veiculo->id)
        ->and($chamado->cliente_id)->toBe($cliente->id)
        ->and($veiculo->fresh()->margem())->toBe(9550.0);

    // um chamado recusado nao deve afetar a margem
    $component->call('novaGarantia')
        ->set('descricao_problema', 'Barulho no motor (nao coberto)')
        ->set('status', 'recusado')
        ->set('custo_peca', 5000)
        ->call('salvarGarantia');

    expect($veiculo->fresh()->margem())->toBe(9550.0);

    GarantiaChamado::where('venda_id', $venda->id)->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
