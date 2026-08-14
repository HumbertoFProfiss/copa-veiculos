<?php

use App\Livewire\Vendas\Show;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\RenaveTransferencia;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('nao registra transferencia renave para venda pendente, registra para venda confirmada, bloqueia segunda enquanto ativa e libera apos cancelar', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'RenaveTeste'.Str::random(6),
        'modelo' => 'R',
        'slug' => Str::slug('renave-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 35000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Renave Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 35000, 'desconto' => 0,
        'status' => 'pendente', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('gerarTransferenciaRenave');
    expect(RenaveTransferencia::where('venda_id', $venda->id)->count())->toBe(0);

    $venda->update(['status' => 'confirmada']);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('gerarTransferenciaRenave');

    $transferencia = RenaveTransferencia::where('venda_id', $venda->id)->first();
    expect($transferencia)->not->toBeNull()
        ->and($transferencia->status)->toBe('concluida')
        ->and($transferencia->protocolo)->toStartWith('RNV');

    // segunda tentativa enquanto a primeira ainda esta concluida nao cria outra
    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('gerarTransferenciaRenave');
    expect(RenaveTransferencia::where('venda_id', $venda->id)->count())->toBe(1);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('cancelarTransferenciaRenave', $transferencia->id);
    expect($transferencia->fresh()->status)->toBe('cancelada')->and($transferencia->fresh()->cancelada_em)->not->toBeNull();

    // apos cancelar a unica transferencia concluida, uma nova passa a ser permitida
    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('gerarTransferenciaRenave');
    expect(RenaveTransferencia::where('venda_id', $venda->id)->where('status', 'concluida')->count())->toBe(1);

    RenaveTransferencia::where('venda_id', $venda->id)->get()->each->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
