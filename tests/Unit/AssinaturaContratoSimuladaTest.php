<?php

use App\Livewire\Contratos\Index;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\ContratoModelo;
use App\Models\Empresa;
use App\Models\Veiculo;
use App\Models\Venda;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(Tests\TestCase::class);

beforeEach(function () {
    usarMysqlRealDeDev();

    $this->empresa = Empresa::first();
    app()->instance('tenant', $this->empresa);
    $this->user = usuarioProprietario($this->empresa);

    $this->veiculo = Veiculo::create([
        'empresa_id' => $this->empresa->id,
        'marca' => 'AssinaturaTeste'.Str::random(6),
        'modelo' => 'A',
        'slug' => Str::slug('assinatura-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'disponivel', 'preco_venda' => 45000,
    ]);
    $this->cliente = Cliente::create(['empresa_id' => $this->empresa->id, 'nome' => 'Cliente Assinatura Teste '.Str::random(4)]);
    $this->venda = Venda::create([
        'empresa_id' => $this->empresa->id, 'veiculo_id' => $this->veiculo->id, 'cliente_id' => $this->cliente->id,
        'vendedor_id' => $this->user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 45000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);
    $modelo = ContratoModelo::where('tipo', 'compra_venda')->where('ativo', true)->first();
    $this->contrato = Contrato::create([
        'contrato_modelo_id' => $modelo->id,
        'venda_id' => $this->venda->id,
        'veiculo_id' => $this->veiculo->id,
        'cliente_id' => $this->cliente->id,
        'criado_por' => $this->user->id,
        'numero' => 'CTR-TESTE-'.Str::random(8),
        'status' => 'rascunho',
        'corpo_html_gerado' => '<p>teste</p>',
    ]);
});

afterEach(function () {
    $this->contrato->delete();
    $this->venda->delete();
    $this->veiculo->delete();
    $this->cliente->delete();
});

it('envia um contrato para assinatura simulada, e o cliente assina (simulacao)', function () {
    Livewire::actingAs($this->user)->test(Index::class)
        ->call('enviarParaAssinatura', $this->contrato->id);

    $this->contrato->refresh();
    expect($this->contrato->status)->toBe('enviado')
        ->and($this->contrato->assinatura_status)->toBe('pendente')
        ->and($this->contrato->assinatura_provider)->toBe('simulado')
        ->and($this->contrato->assinatura_metadata['simulacao'])->toBeTrue();

    Livewire::actingAs($this->user)->test(Index::class)
        ->call('marcarAssinado', $this->contrato->id);

    $this->contrato->refresh();
    expect($this->contrato->status)->toBe('assinado')
        ->and($this->contrato->assinatura_status)->toBe('assinado')
        ->and($this->contrato->assinatura_metadata['assinado_em'])->not->toBeNull();
});

it('recusa a assinatura simulada quando o cliente nao assina', function () {
    Livewire::actingAs($this->user)->test(Index::class)->call('enviarParaAssinatura', $this->contrato->id);
    Livewire::actingAs($this->user)->test(Index::class)->call('recusarAssinatura', $this->contrato->id);

    $this->contrato->refresh();
    expect($this->contrato->status)->toBe('enviado')
        ->and($this->contrato->assinatura_status)->toBe('recusado');
});

it('filtra a listagem de contratos por numero, nome do cliente e carro vendido', function () {
    $this->contrato->update(['numero' => 'CTR-FILTRO-'.Str::random(6)]);

    Livewire::actingAs($this->user)->test(Index::class)
        ->set('busca', $this->contrato->numero)
        ->assertSee($this->contrato->numero);

    Livewire::actingAs($this->user)->test(Index::class)
        ->set('busca', $this->cliente->nome)
        ->assertSee($this->contrato->numero);

    Livewire::actingAs($this->user)->test(Index::class)
        ->set('busca', $this->veiculo->marca)
        ->assertSee($this->contrato->numero);

    Livewire::actingAs($this->user)->test(Index::class)
        ->set('busca', 'TermoQueNaoExisteEmNenhumContrato'.Str::random(6))
        ->assertDontSee($this->contrato->numero);
});
