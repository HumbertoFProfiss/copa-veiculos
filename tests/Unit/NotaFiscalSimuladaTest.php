<?php

use App\Livewire\Vendas\Show;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\NotaFiscal;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('nao emite nota fiscal para venda pendente, emite para venda confirmada, bloqueia segunda emissao e libera apos cancelar', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'NfeTeste'.Str::random(6),
        'modelo' => 'N',
        'slug' => Str::slug('nfe-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 30000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Nfe Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 30000, 'desconto' => 1000,
        'status' => 'pendente', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('emitirNotaFiscal');
    expect(NotaFiscal::where('venda_id', $venda->id)->count())->toBe(0);

    $venda->update(['status' => 'confirmada']);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('emitirNotaFiscal');

    $nota = NotaFiscal::where('venda_id', $venda->id)->first();
    expect($nota)->not->toBeNull()
        ->and($nota->status)->toBe('emitida')
        ->and(strlen($nota->chave_acesso))->toBe(44)
        ->and((float) $nota->valor)->toBe(29000.0);

    // segunda emissao enquanto a primeira ainda esta emitida nao cria outra nota
    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('emitirNotaFiscal');
    expect(NotaFiscal::where('venda_id', $venda->id)->count())->toBe(1);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('cancelarNotaFiscal', $nota->id);
    expect($nota->fresh()->status)->toBe('cancelada')->and($nota->fresh()->cancelada_em)->not->toBeNull();

    // apos cancelar a unica nota emitida, uma nova emissao passa a ser permitida
    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda->fresh()])
        ->call('emitirNotaFiscal');
    expect(NotaFiscal::where('venda_id', $venda->id)->where('status', 'emitida')->count())->toBe(1);

    NotaFiscal::where('venda_id', $venda->id)->get()->each->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
