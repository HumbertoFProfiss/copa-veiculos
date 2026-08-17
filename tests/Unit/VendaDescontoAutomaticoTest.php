<?php

use App\Livewire\Vendas\Nova;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Veiculo;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('calcula o desconto automaticamente quando o valor de venda e menor que o preco anunciado do veiculo', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'DescontoAutoTeste'.Str::random(6),
        'modelo' => 'D',
        'slug' => Str::slug('desconto-auto-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 89900,
    ]);

    $componente = Livewire::actingAs($user)->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->assertSet('preco_venda', 89900.0)
        ->assertSet('desconto', 0);

    $componente->set('preco_venda', 87000)
        ->assertSet('desconto', 2900.0);

    // se o vendedor digitar um valor MAIOR que o anunciado, nao gera desconto negativo
    $componente->set('preco_venda', 95000)
        ->assertSet('desconto', 0);

    $veiculo->delete();
});

it('nao mexe no desconto se nenhum veiculo foi selecionado ainda', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Livewire::actingAs($user)->test(Nova::class)
        ->set('preco_venda', 50000)
        ->assertSet('desconto', 0);
});
