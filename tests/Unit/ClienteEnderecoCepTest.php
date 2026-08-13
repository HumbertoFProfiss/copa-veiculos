<?php

use App\Livewire\Clientes\Index;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Venda;
use App\Services\Contratos\ContratoRenderer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(fn () => usarMysqlRealDeDev());

it('busca o cep, preenche o endereco e salva o cliente', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    Http::fake([
        'viacep.com.br/ws/*' => Http::response([
            'logradouro' => 'Rua das Flores',
            'bairro' => 'Centro',
            'localidade' => 'Lins',
            'uf' => 'SP',
        ]),
    ]);

    $nome = 'ClienteCepTeste'.Str::random(6);

    Livewire::actingAs($user)->test(Index::class)
        ->call('novo')
        ->set('nome', $nome)
        ->set('cep', '16400-000')
        ->call('buscarCep')
        ->assertSet('endereco', 'Rua das Flores - Centro')
        ->assertSet('cidade', 'Lins')
        ->assertSet('uf', 'SP')
        ->call('salvar');

    $cliente = Cliente::where('nome', $nome)->first();
    expect($cliente)->not->toBeNull()
        ->and($cliente->endereco)->toBe('Rua das Flores - Centro')
        ->and($cliente->cidade)->toBe('Lins')
        ->and($cliente->cep)->toBe('16400-000');

    $cliente->delete();
});

it('puxa o endereco do cliente no contrato gerado', function () {
    $empresa = Empresa::first();
    app()->instance('tenant', $empresa);
    $user = usuarioProprietario($empresa);

    $cliente = Cliente::create([
        'empresa_id' => $empresa->id,
        'nome' => 'Cliente Contrato Teste '.Str::random(4),
        'cpf' => null,
        'endereco' => 'Rua das Flores - Centro',
        'cidade' => 'Lins',
        'uf' => 'SP',
        'cep' => '16400-000',
    ]);

    $veiculo = Veiculo::create([
        'empresa_id' => $empresa->id,
        'marca' => 'ContratoTeste'.Str::random(6),
        'modelo' => 'C',
        'slug' => Str::slug('contrato-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_venda' => 45000,
    ]);

    $venda = Venda::create([
        'empresa_id' => $empresa->id,
        'veiculo_id' => $veiculo->id,
        'cliente_id' => $cliente->id,
        'vendedor_id' => $user->id,
        'forma_pagamento' => 'avista',
        'preco_venda' => 45000,
        'desconto' => 0,
        'status' => 'confirmada',
        'data_venda' => now(),
        'prazo_garantia_dias' => 90,
    ]);

    $html = (new ContratoRenderer)->renderizar('Endereço do comprador: {{cliente.endereco}}', $venda, $empresa);

    expect($html)->toBe('Endereço do comprador: Rua das Flores - Centro, Lins, SP, 16400-000');

    $venda->delete();
    $veiculo->delete();
    $cliente->delete();
});
