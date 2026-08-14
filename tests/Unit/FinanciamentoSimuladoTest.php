<?php

use App\Livewire\Vendas\Show;
use App\Models\Banco;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\PropostaFinanciamento;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('simula uma proposta de financiamento com a parcela calculada pela tabela Price, e permite aprovar/recusar', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'FinanciamentoTeste'.Str::random(6),
        'modelo' => 'F',
        'slug' => Str::slug('financiamento-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Financiamento Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'financiado', 'preco_venda' => 50000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);
    $banco = Banco::where('slug', 'itau_veiculos')->first();

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('abrirFormFinanciamento')
        ->set('financiamento_banco_id', $banco->id)
        ->set('financiamento_valor_financiado', 40000)
        ->set('financiamento_entrada', 10000)
        ->set('financiamento_num_parcelas', 48)
        ->call('simularFinanciamento')
        ->assertHasNoErrors();

    $proposta = PropostaFinanciamento::where('venda_id', $venda->id)->first();
    expect($proposta)->not->toBeNull()
        ->and($proposta->status)->toBe('simulada')
        ->and((float) $proposta->taxa_juros_am)->toBe((float) $banco->taxa_juros_am_padrao);

    $esperado = PropostaFinanciamento::calcularParcela(40000, (float) $banco->taxa_juros_am_padrao, 48);
    expect((float) $proposta->valor_parcela)->toBe($esperado)->and($esperado)->toBeGreaterThan(40000 / 48);

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('aprovarProposta', $proposta->id);
    expect($proposta->fresh()->status)->toBe('aprovada');

    Livewire::actingAs($user)->test(Show::class, ['venda' => $venda])
        ->call('recusarProposta', $proposta->id);
    expect($proposta->fresh()->status)->toBe('recusada');

    $proposta->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});

it('calcula parcela sem juros como divisao simples', function () {
    expect(PropostaFinanciamento::calcularParcela(12000, 0, 12))->toBe(1000.0);
});
