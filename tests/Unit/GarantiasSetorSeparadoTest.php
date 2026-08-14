<?php

use App\Livewire\Garantias\Index as GarantiasIndex;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\GarantiaChamado;
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
        'marca' => 'GarantiaSetorTeste'.Str::random(6),
        'modelo' => 'S',
        'slug' => Str::slug('garantia-setor-teste-'.Str::random(8)),
        'km' => 0, 'portas' => 4, 'tipo_propriedade' => 'proprio',
        'data_entrada' => now(), 'status' => 'vendido', 'preco_venda' => 50000,
    ]);
    $this->cliente = Cliente::create(['empresa_id' => $this->empresa->id, 'nome' => 'Cliente Garantia Setor Teste '.Str::random(4)]);
    $this->venda = Venda::create([
        'empresa_id' => $this->empresa->id, 'veiculo_id' => $this->veiculo->id, 'cliente_id' => $this->cliente->id,
        'vendedor_id' => $this->user->id, 'forma_pagamento' => 'avista', 'preco_venda' => 50000, 'desconto' => 0,
        'status' => 'confirmada', 'data_venda' => now(), 'prazo_garantia_dias' => 90,
    ]);
    $this->chamadoAberto = GarantiaChamado::create([
        'empresa_id' => $this->empresa->id, 'venda_id' => $this->venda->id, 'veiculo_id' => $this->veiculo->id, 'cliente_id' => $this->cliente->id,
        'descricao_problema' => 'Barulho na suspensão teste setor',
        'status' => 'aberto', 'custo_peca' => 200, 'custo_servico' => 100,
    ]);
    $this->chamadoAprovado = GarantiaChamado::create([
        'empresa_id' => $this->empresa->id, 'venda_id' => $this->venda->id, 'veiculo_id' => $this->veiculo->id, 'cliente_id' => $this->cliente->id,
        'descricao_problema' => 'Vidro elétrico teste setor',
        'status' => 'aprovado', 'custo_peca' => 500, 'custo_servico' => 300,
    ]);
});

afterEach(function () {
    GarantiaChamado::where('venda_id', $this->venda->id)->delete();
    $this->venda->delete();
    $this->veiculo->delete();
    $this->cliente->delete();
});

it('lista os chamados de garantia de todas as vendas num setor separado, com link pra venda', function () {
    Livewire::actingAs($this->user)->test(GarantiasIndex::class)
        ->assertSee($this->veiculo->marca)
        ->assertSee('Barulho na suspensão teste setor')
        ->assertSee('Vidro elétrico teste setor')
        ->assertSee('R$ 300,00') // aberto: 200+100
        ->assertSee('R$ 800,00'); // aprovado: 500+300
});

it('filtra por status e por busca de veiculo/cliente/descricao', function () {
    Livewire::actingAs($this->user)->test(GarantiasIndex::class)
        ->set('filtroStatus', 'aprovado')
        ->assertSee('Vidro elétrico teste setor')
        ->assertDontSee('Barulho na suspensão teste setor');

    Livewire::actingAs($this->user)->test(GarantiasIndex::class)
        ->set('busca', $this->cliente->nome)
        ->assertSee('Barulho na suspensão teste setor')
        ->assertSee('Vidro elétrico teste setor');
});

it('o link do menu lateral e a rota de garantias estao acessiveis pra quem tem permissao de vendas', function () {
    $this->actingAs($this->user)
        ->get(route('admin.garantias.index'))
        ->assertOk()
        ->assertSee('Garantias');
});

it('cria um novo chamado de garantia direto na tela de garantias, escolhendo a venda', function () {
    Livewire::actingAs($this->user)->test(GarantiasIndex::class)
        ->call('novaGarantia')
        ->set('venda_id', $this->venda->id)
        ->set('descricao_problema', 'Chave reserva nao funciona')
        ->set('novoStatus', 'aberto')
        ->set('custo_peca', 80)
        ->set('custo_servico', 0)
        ->call('salvarGarantia')
        ->assertHasNoErrors()
        ->assertSee('Chave reserva nao funciona');

    $chamado = GarantiaChamado::where('venda_id', $this->venda->id)
        ->where('descricao_problema', 'Chave reserva nao funciona')
        ->first();

    expect($chamado)->not->toBeNull()
        ->and($chamado->veiculo_id)->toBe($this->veiculo->id)
        ->and($chamado->cliente_id)->toBe($this->cliente->id)
        ->and((float) $chamado->custo_peca)->toBe(80.0);
});

it('exige a selecao de uma venda pra criar o chamado', function () {
    Livewire::actingAs($this->user)->test(GarantiasIndex::class)
        ->call('novaGarantia')
        ->set('descricao_problema', 'Sem venda selecionada')
        ->call('salvarGarantia')
        ->assertHasErrors(['venda_id']);
});
