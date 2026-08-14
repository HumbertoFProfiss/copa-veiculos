<?php

use App\Livewire\Vendas\Nova;
use App\Livewire\Vendas\Show;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('venda pendente nao reserva o veiculo nem dispara efeitos de confirmacao', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaPendenteTeste'.Str::random(6),
        'modelo' => 'P',
        'slug' => Str::slug('venda-pendente-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 40000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Pendente Teste '.Str::random(4)]);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 40000)
        ->set('status', 'pendente')
        ->call('salvar');

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull()
        ->and($venda->status)->toBe('pendente')
        ->and($veiculo->fresh()->status)->toBe('disponivel');

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('confirmar uma venda pendente reserva o veiculo, e cancelar depois devolve pro estoque', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'VendaConfirmaTeste'.Str::random(6),
        'modelo' => 'C',
        'slug' => Str::slug('venda-confirma-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 40000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Confirma Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 40000, 'desconto' => 0,
        'status' => 'pendente', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('confirmarVenda');

    expect($venda->fresh()->status)->toBe('confirmada')
        ->and($veiculo->fresh()->status)->toBe('vendido');

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('cancelarVenda');

    expect($venda->fresh()->status)->toBe('cancelada')
        ->and($veiculo->fresh()->status)->toBe('disponivel');

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
