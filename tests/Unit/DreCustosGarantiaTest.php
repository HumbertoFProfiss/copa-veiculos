<?php

use App\Livewire\Financeiro\Dre;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\GarantiaChamado;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('inclui os custos de garantia aprovados/concluidos no calculo do DRE do periodo da venda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    // baseline antes de criar o dado de teste, porque o DRE soma TODAS as
    // vendas confirmadas do mes atual - precisa comparar a diferenca (delta),
    // nao o valor absoluto (mesmo padrao usado no DashboardFinanceiroTest).
    $baseline = Livewire::actingAs($user)->test(Dre::class)->instance()->calcular();
    $custosGarantiaBase = (float) $baseline['custosGarantia'];
    $lucroBase = (float) $baseline['lucroLiquido'];

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'DreGarantiaTeste'.Str::random(6),
        'modelo' => 'D',
        'slug' => Str::slug('dre-garantia-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_compra' => 30000, 'preco_venda' => 40000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Dre Garantia Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 40000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    $garantiaAprovada = GarantiaChamado::create([
        'empresa_id' => $empresa->id, 'venda_id' => $venda->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'descricao_problema' => 'Teste garantia aprovada DRE', 'status' => 'aprovado', 'custo_peca' => 170, 'custo_servico' => 0,
    ]);
    $garantiaConcluida = GarantiaChamado::create([
        'empresa_id' => $empresa->id, 'venda_id' => $venda->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'descricao_problema' => 'Teste garantia concluida DRE', 'status' => 'concluido', 'custo_peca' => 0, 'custo_servico' => 430,
    ]);
    // uma garantia recusada NAO deve entrar na conta
    $garantiaRecusada = GarantiaChamado::create([
        'empresa_id' => $empresa->id, 'venda_id' => $venda->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'descricao_problema' => 'Teste garantia recusada DRE', 'status' => 'recusado', 'custo_peca' => 5000, 'custo_servico' => 0,
    ]);

    $componenteTeste = Livewire::actingAs($user)->test(Dre::class);
    $resultado = $componenteTeste->instance()->calcular();

    $deltaCustosGarantia = (float) $resultado['custosGarantia'] - $custosGarantiaBase;
    expect($deltaCustosGarantia)->toBe(600.0); // 170 + 430, sem contar a recusada

    $deltaLucro = (float) $resultado['lucroLiquido'] - $lucroBase;
    // receita 40000 - custo veiculo 30000 - custos garantia 600 - comissao 0 = 9400
    expect($deltaLucro)->toBe(9400.0);

    $componenteTeste->assertSee('Custos de garantia');

    GarantiaChamado::whereIn('id', [$garantiaAprovada->id, $garantiaConcluida->id, $garantiaRecusada->id])->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
