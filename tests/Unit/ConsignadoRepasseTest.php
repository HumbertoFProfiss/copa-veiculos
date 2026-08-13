<?php

use App\Livewire\Vendas\Nova;
use App\Models\Cliente;
use App\Models\ContaPagar;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('calcula comissao fixa e percentual do consignado corretamente', function () {
    $veiculoFixa = new Veiculo([
        'tipo_propriedade' => 'consignado',
        'consignado_comissao_tipo' => 'fixa',
        'consignado_comissao_valor' => 2000,
    ]);
    expect($veiculoFixa->comissaoConsignado(50000))->toBe(2000.0)
        ->and($veiculoFixa->repasseConsignado(50000))->toBe(48000.0);

    $veiculoPercentual = new Veiculo([
        'tipo_propriedade' => 'consignado',
        'consignado_comissao_tipo' => 'percentual',
        'consignado_comissao_valor' => 10,
    ]);
    expect($veiculoPercentual->comissaoConsignado(50000))->toBe(5000.0)
        ->and($veiculoPercentual->repasseConsignado(50000))->toBe(45000.0);
});

it('gera repasse automatico em contas a pagar quando um veiculo consignado e vendido', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'ConsignadoTeste'.Str::random(6),
        'modelo' => 'W',
        'slug' => Str::slug('consignado-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'consignado',
        'consignado_nome' => 'João da Silva',
        'consignado_telefone' => '(14) 99999-0000',
        'consignado_comissao_tipo' => 'percentual',
        'consignado_comissao_valor' => 10,
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 50000,
    ]);

    $cliente = Cliente::create(['empresa_id' => $empresa->id, 'nome' => 'Cliente Consignado Teste '.Str::random(4)]);

    Livewire::actingAs($user)
        ->test(Nova::class)
        ->set('veiculo_id', $veiculo->id)
        ->set('cliente_id', $cliente->id)
        ->set('vendedor_id', $user->id)
        ->set('preco_venda', 50000)
        ->call('salvar');

    $venda = Venda::where('veiculo_id', $veiculo->id)->first();
    expect($venda)->not->toBeNull();

    $contaPagar = ContaPagar::where('descricao', 'like', '%João da Silva%')->first();
    expect($contaPagar)->not->toBeNull()
        ->and((float) $contaPagar->valor)->toBe(45000.0)
        ->and($contaPagar->status)->toBe('pendente')
        ->and($contaPagar->categoria->nome)->toBe('Repasse de consignação');

    $contaPagar->delete();
    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
