<?php

use App\Livewire\Relatorios\Construtor;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('lista veiculos com as colunas selecionadas e respeita o filtro de periodo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $marcaUnica = 'RelatorioTeste'.Str::random(6);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => $marcaUnica,
        'modelo' => 'Modelo X',
        'slug' => Str::slug($marcaUnica.'-'.Str::random(4)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 77000,
    ]);

    Livewire::actingAs($user)
        ->test(Construtor::class)
        ->assertSee($marcaUnica);

    // Filtro de periodo (data de cadastro = hoje) que exclui tudo antes de amanha.
    Livewire::actingAs($user)
        ->test(Construtor::class)
        ->set('dataInicio', now()->addDay()->format('Y-m-d'))
        ->assertDontSee($marcaUnica);

    $veiculo->delete();
});

it('agrupa por status e soma o valor corretamente', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $marca = 'RelatorioGrupo'.Str::random(6);

    $v1 = Veiculo::create([
        'empresa_id' => $empresa->id, 'marca' => $marca, 'modelo' => 'A',
        'slug' => Str::slug($marca.'-a-'.Str::random(4)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 10000,
    ]);
    $v2 = Veiculo::create([
        'empresa_id' => $empresa->id, 'marca' => $marca, 'modelo' => 'B',
        'slug' => Str::slug($marca.'-b-'.Str::random(4)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 15000,
    ]);

    $component = Livewire::actingAs($user)
        ->test(Construtor::class)
        ->set('agruparPor', 'status');

    $agrupado = $component->viewData('agrupado');
    $linhaDisponivel = collect($agrupado)->firstWhere('status', 'disponivel');

    expect($linhaDisponivel)->not->toBeNull()
        ->and($linhaDisponivel->quantidade)->toBeGreaterThanOrEqual(2)
        ->and((float) $linhaDisponivel->soma)->toBeGreaterThanOrEqual(25000.0);

    $component->assertSee('Disponível');

    $v1->delete();
    $v2->delete();
});

it('bloqueia agrupar por um campo fora da whitelist da fonte (protecao contra injecao)', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $component = Livewire::actingAs($user)
        ->test(Construtor::class)
        ->set('agruparPor', 'id) UNION SELECT password FROM users --');

    expect($component->viewData('agrupado'))->toBeNull();
    $component->assertOk();
});

it('exporta o relatorio sem erro', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $resposta = Livewire::actingAs($user)
        ->test(Construtor::class)
        ->call('exportar');

    expect($resposta)->not->toBeNull();
});
