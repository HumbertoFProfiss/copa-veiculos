<?php

use App\Livewire\Financeiro\Categorias;
use App\Livewire\Fornecedores\Index as FornecedoresIndex;
use App\Livewire\Garantias\Index as GarantiasIndex;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use App\Livewire\Vendas\Show as VendasShow;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('modal de usuarios nao fecha ao clicar fora, e tem botao X', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(UsuariosIndex::class)
        ->call('novo')
        ->html();

    expect($html)->not->toContain('wire:click.self')
        ->and($html)->toContain('aria-label="Fechar"');
});

it('modal de fornecedores nao fecha ao clicar fora, e tem botao X', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(FornecedoresIndex::class)
        ->call('novo')
        ->html();

    expect($html)->not->toContain('wire:click.self')
        ->and($html)->toContain('aria-label="Fechar"');
});

it('modal de categorias financeiras nao fecha ao clicar fora, e tem botao X', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(Categorias::class)
        ->call('novo')
        ->html();

    expect($html)->not->toContain('wire:click.self')
        ->and($html)->toContain('aria-label="Fechar"');
});

it('modal de novo chamado de garantia (setor separado) nao fecha ao clicar fora, e tem botao X', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $html = Livewire::actingAs($user)->test(GarantiasIndex::class)
        ->call('novaGarantia')
        ->html();

    expect($html)->not->toContain('wire:click.self')
        ->and($html)->toContain('aria-label="Fechar"');
});

it('modais de financiamento e garantia dentro da venda nao fecham ao clicar fora, e tem botao X', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'ModalXTeste'.Str::random(6),
        'modelo' => 'M',
        'slug' => Str::slug('modal-x-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_venda' => 40000,
    ]);
    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Modal X Teste '.Str::random(4)]);
    $venda = Venda::create([
        'empresa_id' => $empresa->id, 'veiculo_id' => $veiculo->id, 'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 40000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);

    $component = Livewire::actingAs($user)->test(VendasShow::class, ['venda' => $venda]);

    $htmlFinanciamento = $component->call('abrirFormFinanciamento')->html();
    expect($htmlFinanciamento)->not->toContain('wire:click.self')
        ->and($htmlFinanciamento)->toContain('aria-label="Fechar"');

    $htmlGarantia = $component->call('novaGarantia')->html();
    expect($htmlGarantia)->not->toContain('wire:click.self')
        ->and($htmlGarantia)->toContain('aria-label="Fechar"');

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
