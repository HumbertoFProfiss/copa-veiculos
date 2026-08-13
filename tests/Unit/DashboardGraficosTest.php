<?php

use App\Livewire\Dashboard;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Lead;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('monta as series dos ultimos 6 meses com a venda e o lead do mes atual', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'DashboardTeste'.Str::random(6),
        'modelo' => 'D',
        'slug' => Str::slug('dashboard-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_venda' => 30000,
    ]);

    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Dashboard '.Str::random(4)]);

    $venda = Venda::create([
        'empresa_id' => $empresa->id,
        'veiculo_id' => $veiculo->id,
        'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id,
        'forma_pagamento' => 'avista',
        'preco_venda' => 30000,
        'desconto' => 0,
        'status' => 'confirmada',
        'data_venda' => now(),
        'prazo_garantia_dias' => 90,
    ]);

    $lead = Lead::create([
        'empresa_id' => $empresa->id,
        'veiculo_id' => $veiculo->id,
        'nome' => 'Lead Dashboard Teste',
        'telefone' => '14999998888',
        'origem' => 'site',
        'estagio' => 'novo',
        'lead_falso' => false,
    ]);

    $component = Livewire::actingAs($user)->test(Dashboard::class);
    $series = $component->viewData('series');

    expect($series['labels'])->toHaveCount(6)
        ->and($series['vendasQtd'])->toHaveCount(6)
        ->and(end($series['vendasQtd']))->toBeGreaterThanOrEqual(1)
        ->and(end($series['vendasReceita']))->toBeGreaterThanOrEqual(30000.0)
        ->and(end($series['leadsQtd']))->toBeGreaterThanOrEqual(1);

    $lead->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
