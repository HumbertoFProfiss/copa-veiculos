<?php

use App\Livewire\Dashboard;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\GarantiaChamado;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('calcula faturamento bruto, lucro liquido, custos pos-venda e ranking de vendedores do mes', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    // Baseline antes de criar os dados do teste - o dashboard soma TODAS as
    // vendas confirmadas do mes (comportamento correto), entao o teste nao
    // pode assumir que o mes esta "zerado" antes de comecar.
    $baseline = Livewire::actingAs($user)->test(Dashboard::class);
    $faturamentoAntes = $baseline->viewData('faturamentoBrutoMes');
    $lucroAntes = $baseline->viewData('lucroLiquidoMes');
    $custosPosVendaAntes = $baseline->viewData('custosPosVendaMes');
    $rankingAntes = $baseline->viewData('rankingVendedores')->firstWhere('vendedor.id', $user->id);
    $quantidadeAntes = $rankingAntes['quantidade'] ?? 0;
    $faturamentoVendedorAntes = $rankingAntes['faturamento'] ?? 0.0;

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'DashFinTeste'.Str::random(6),
        'modelo' => 'D',
        'slug' => Str::slug('dash-fin-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_compra' => 40000, 'preco_venda' => 50000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Dash Fin '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 50000, 'desconto' => 1000,
        'comissao_vendedor' => 500, 'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);
    $garantia = GarantiaChamado::create([
        'empresa_id' => $empresa->id, 'venda_id' => $venda->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'descricao_problema' => 'Teste dashboard', 'status' => 'aprovado', 'custo_peca' => 200, 'custo_servico' => 100,
    ]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    // faturamento = 50000 - 1000 = 49000
    expect($component->viewData('faturamentoBrutoMes') - $faturamentoAntes)->toBe(49000.0);

    // lucro = 49000 - 40000(compra) - 0(custos veiculo) - 300(garantia) - 500(comissao) = 8200
    expect(round($component->viewData('lucroLiquidoMes') - $lucroAntes, 2))->toBe(8200.0);

    expect($component->viewData('custosPosVendaMes') - $custosPosVendaAntes)->toBe(300.0);

    $rankingDepois = $component->viewData('rankingVendedores')->firstWhere('vendedor.id', $user->id);
    expect($rankingDepois)->not->toBeNull()
        ->and($rankingDepois['quantidade'] - $quantidadeAntes)->toBe(1)
        ->and($rankingDepois['faturamento'] - $faturamentoVendedorAntes)->toBe(49000.0);

    $garantia->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
